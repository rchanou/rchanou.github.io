-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[CheckDetails_V] AS
WITH CONSTANTS1 AS (
    SELECT
        CAST(cp.SettingValue AS BIT) AS USE_SALES_TAX
    FROM dbo.ControlPanel cp
    WHERE
        cp.TerminalName = 'MainEngine'
        AND cp.SettingName = 'UseSalesTax'
)
, CONSTANTS2 AS (
    SELECT TOP 1
        CAST(cp.SettingValue AS DATETIME) AS CHECK_CALCULATION_SWITCHOVER_DATE_V1552
    FROM dbo.ControlPanel cp
    WHERE
        cp.SettingName = 'CheckCalculationSwitchDateV1552'
)
, CONSTANTS3 AS (
    SELECT TOP 1
        CAST(cp.SettingValue AS DATETIME) AS CHECK_CALCULATION_SWITCHOVER_DATE_V1554
    FROM dbo.ControlPanel cp
    WHERE
        cp.SettingName = 'CheckCalculationSwitchDateV1554'
)
, CONSTANTS AS (
    SELECT TOP 1 -- there should only ever be 1 record.
          ISNULL(c1.USE_SALES_TAX, 0) AS USE_SALES_TAX
        , ISNULL(c2.CHECK_CALCULATION_SWITCHOVER_DATE_V1552, CAST('1753-01-01T00:00:00' AS DATETIME)) AS CHECK_CALCULATION_SWITCHOVER_DATE_V1552
        , ISNULL(c3.CHECK_CALCULATION_SWITCHOVER_DATE_V1554, CAST('1753-01-01T00:00:00' AS DATETIME)) AS CHECK_CALCULATION_SWITCHOVER_DATE_V1554
    FROM CONSTANTS1 c1
    OUTER APPLY CONSTANTS2 c2
    OUTER APPLY CONSTANTS3 c3
)
, CheckDetailSums1 AS (
    SELECT
          cd.CheckDetailID
        , cd.CheckID
        , cd.UnitPrice
        , cd.Qty + cd.CadetQty AS CheckDetailActualQuantity
        , cd.DiscountApplied
        , cd.TaxPercent
        , cd.GST
        , cd.TaxPercent / 100.0 AS CheckDetailTaxPercentage
        , cd.GST / 100.0 AS CheckDetailGSTPercentage
        , cd.TaxPercent / 100.0  as OldCheckDetailTaxPercentage
        , cd.TaxPercent / 100.0  as MiddleCheckDetailTaxPercentage
        , cd.TaxPercent / 100.0  as NewCheckDetailTaxPercentage
    FROM dbo.CheckDetails cd
    OUTER APPLY CONSTANTS
    WHERE cd.Status <> 2 -- Voided
)
, CheckDetailSums2 AS (
    SELECT
          cds.CheckID
        , cds.DiscountApplied
        , cds.CheckDetailID
        , cds.UnitPrice
        , cds.CheckDetailActualQuantity
        , cds.CheckDetailTaxPercentage
        , cds.CheckDetailGSTPercentage
        , CASE
            WHEN cds.CheckDetailActualQuantity = 0 THEN
                0
            ELSE
                cds.UnitPrice - ISNULL(cds.DiscountApplied,0) / CheckDetailActualQuantity
        END AS UnitPriceAfterDiscount
    FROM CheckDetailSums1 cds
)
, CheckDetailSums3 AS (
    SELECT 
          cds.CheckID
        , cds.DiscountApplied
        , cds.CheckDetailID
        , cds.UnitPrice
        , cds.CheckDetailActualQuantity
        , cds.UnitPriceAfterDiscount
        , (
            CASE
                WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                    ROUND(cds.UnitPriceAfterDiscount * cds.CheckDetailTaxPercentage, 2)
                ELSE
                    cds.UnitPriceAfterDiscount - ROUND(cds.UnitPriceAfterDiscount  / (cds.CheckDetailTaxPercentage + 1.0), 2) -- 10% VAT of 1.00 should be .09, not .10 (x * 1.1 = 1.00)
            END
        ) AS CheckDetailSingleTaxAmount
        , (
            CASE
                WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                    ROUND(cds.UnitPriceAfterDiscount * cds.CheckDetailGSTPercentage, 2)
                ELSE
                    0 -- GST is not relevant for VAT
            END
        ) AS CheckDetailSingleGSTAmount
    FROM CheckDetailSums2 cds
    OUTER APPLY CONSTANTS
)
, CheckDetailSums4 AS (
    SELECT
          cds.CheckDetailID
        , cds.CheckID
        , cds.DiscountApplied
        , cds.CheckDetailSingleGSTAmount * cds.CheckDetailActualQuantity AS CheckDetailGST
        , (
            CASE
                WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                    cds.CheckDetailSingleTaxAmount - cds.CheckDetailSingleGSTAmount -- use subtraction use up the remainder of the tax sum, in order to avoid rounding errors
                ELSE
                    0 -- PST is not relevant with VAT
            END
        ) AS CheckDetailSinglePSTAmount
        , cds.CheckDetailActualQuantity
        , cds.CheckDetailSingleTaxAmount * cds.CheckDetailActualQuantity AS CheckDetailTax
        , cds.UnitPriceAfterDiscount * cds.CheckDetailActualQuantity AS CheckDetailSubtotal
    FROM CheckdetailSums3 cds
    OUTER APPLY CONSTANTS
)
, CheckDetailSums5 AS (
    SELECT
          cds.CheckDetailID
        , cds.CheckID
        , cds.CheckDetailTax
        , cds.CheckDetailGST
        , cds.CheckDetailSinglePSTAmount * cds.CheckDetailActualQuantity AS CheckDetailPST
        , cds.CheckDetailSubtotal
        , (
            cds.CheckDetailSubtotal
            + (
                CASE
                    WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                        cds.CheckDetailTax
                    ELSE
                        0 -- Tax is included with the Subtotal when using VAT
                END
            )
        ) AS CheckDetailTotal
    FROM CheckDetailSums4 cds
    OUTER APPLY CONSTANTS
)
, CheckDetailOverride AS (
    -- Mega cheat to ensure that old reports can still use the old calculation system,
    -- but also so we can use the new (correct) calculation system moving forward.
    SELECT
        CASE
            WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                'NEW_METHOD'
            WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
               'BETWEEN'   
            ELSE
                'OLD_METHOD'
        END AS [Method]
        , cds.CheckDetailID
        , cds.CheckID
        , (
            -- CheckDetailSubtotal
            CASE
                WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    cds.CheckDetailSubtotal
                WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    ISNULL((UnitPrice * (Qty + CadetQty) - DiscountApplied) , 0)
                ELSE
                    -- calculations taken from dbo.ApplyCheckTotal stored proc
                    ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)
            END
        ) AS CheckDetailSubtotal
        , (
            -- CheckDetailTax
            CASE
                WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    -- NEW
                    cds.CheckDetailTax
                WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                  --BETWEEN
                  CASE
                        WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                            -- ((SUBTOTAL * COMPOUND RATE) / 100.0)
                            (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - ROUND(cd.DiscountApplied,2)), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100) / 100.00
                        ELSE
                            -- SUBTOTAL * GST / (100 + GST)
                            (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent)
                    END 
                ELSE
                    --OLD
                    ROUND(
                        CASE
                            WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                                -- ((SUBTOTAL * COMPOUND RATE) / 100.0)
                                ((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100)) / 100.0
                            ELSE
                                -- SUBTOTAL * GST / (100 + GST)
                                (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent)
                        END
                        , 2
                    )
            END
        ) AS CheckDetailTax
        , (
            -- CheckDetailGST
            CASE
                WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    cds.CheckDetailGST
                WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                  CASE
                        WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                            -- ((SUBTOTAL * GST) / 100.0)
                            (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST) / 100.0)
                        ELSE
                            -- SUBTOTAL * GST / (100 + GST)
                            (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST / (100 + cd.GST)
                    END
                ELSE
                    ROUND(   
                        CASE
                            WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                                -- ((SUBTOTAL * GST) / 100.0)
                                (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST) / 100.0)
                            ELSE
                             -- SUBTOTAL * GST / (100 + GST)
                                (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST / (100 + cd.GST)
                   
                        END
                        , 2                
                    )
            END
        ) AS CheckDetailGST
        , (
            -- CheckDetailPST
            CASE
                WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    cds.CheckDetailPST
                WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    CASE
                        WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                            -- (TOTALTAX) - (TOTALGST)
                              (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100)) / 100.0)
                            - (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST) / 100.0)
                        ELSE
                            -- (TOTALTAX) - (TOTALGST)
                              ((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent))
                            - (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST / (100 + cd.GST)
                    END
                 
                ELSE
                    -- calculations taken from Interfaces\Models\Check\CheckDetail.vb
                    ROUND( 
                        CASE
                            WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                                -- (TOTALTAX) - (TOTALGST)
                                  (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100)) / 100.0)
                                - (((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST) / 100.0)
                            ELSE
                                -- (TOTALTAX) - (TOTALGST)
                                  ((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent))
                                - (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.GST / (100 + cd.GST)
                        END
                        , 2
                   )
            END
        ) AS CheckDetailPST
        , (
            -- CheckDetailTotal
            CASE
                WHEN c.ClosedDate IS NULL OR c.ClosedDate >= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    cds.CheckDetailTotal
                WHEN CHECK_CALCULATION_SWITCHOVER_DATE_V1552 < c.ClosedDate  AND  c.ClosedDate <= CONSTANTS.CHECK_CALCULATION_SWITCHOVER_DATE_V1554 THEN
                    ISNULL((UnitPrice * (Qty + CadetQty) -  round(DiscountApplied,2)), 0)
                    + CASE
                        WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                            -- ((SUBTOTAL * COMPOUND RATE) / 100.0)
                            (ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - round(cd.DiscountApplied,2)), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100) / 100.00
                        ELSE
                            -- SUBTOTAL * GST / (100 + GST)
                            0
                            --(ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent)
                    END
                ELSE
                    ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0) -- subtotal
                    + ROUND(
                        CASE
                            WHEN CONSTANTS.USE_SALES_TAX = 1 THEN
                                -- ((SUBTOTAL * COMPOUND RATE) / 100.0)
                                ((ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * ((((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100)) / 100.0
                            ELSE
                                -- SUBTOTAL * GST / (100 + GST)
                                0
                                --(ISNULL((cd.UnitPrice * (cd.Qty + cd.CadetQty) - cd.DiscountApplied), 0)) * cd.TaxPercent / (100 + cd.TaxPercent)
                        END
                        , 2
                    )
            END
        ) AS CheckDetailTotal
    FROM CheckDetailSums5 cds
    INNER JOIN dbo.Checks c
        ON cds.CheckID = c.CheckID
    INNER JOIN dbo.CheckDetails cd
        ON cd.CheckDetailID = cds.CheckDetailID
    OUTER APPLY CONSTANTS
)
SELECT
      cd.CheckID
    , cd.CheckDetailID
    , ISNULL(cds.CheckDetailSubtotal, 0) AS CheckDetailSubtotal
    , ISNULL(cds.CheckDetailTax, 0) AS CheckDetailTax
    , ISNULL(cds.CheckDetailGST, 0) AS CheckDetailGST
    , ISNULL(cds.CheckDetailPST, 0) AS CheckDetailPST
    , ISNULL(cds.CheckDetailTotal, 0) AS CheckDetailTotal
    , cd.Status AS CheckDetailStatus
    , cd.Type AS CheckDetailType
    , cd.ProductID
    , cd.ProductName
    , cd.CreatedDate
    -- , cd.CreatedOn
    -- , cd.CreatedBy
    , cd.Qty
    , cd.UnitPrice
    , cd.UnitPrice2
    , cd.DiscountApplied
    , cd.TaxID
    , cd.TaxPercent
    , cd.VoidNotes
    , cd.CID
    , cd.VID
    , cd.BonusValue
    , cd.PaidValue
    , cd.ComValue
    , cd.Entitle1
    , cd.Entitle2
    , cd.Entitle3
    , cd.Entitle4
    , cd.Entitle5
    , cd.Entitle6
    , cd.Entitle7
    , cd.Entitle8
    , cd.M_Points
    , cd.M_CustID
    , cd.M_OldMembershiptypeID
    , cd.M_NewMembershiptypeID
    , cd.M_Days
    , cd.M_PrimaryMembership
    , cd.P_PointTypeID
    , cd.P_Points
    , cd.P_CustID
    , cd.R_Points
    , cd.DiscountUserID 
    , cd.DiscountDesc 
    , cd.CalculateType 
    , cd.DiscountID 
    , cd.DiscountNotes 
    , cd.G_Points
    , cd.G_CustID
    , cd.GST
    , cd.M_DaysAdded
    , cd.S_SaleBy
    , cd.S_NoOfLapsOrSeconds
    , cd.S_CustID
    , cd.S_Vol
    , cd.CadetQty
    , [Method]
    , CONSTANTS.*
FROM CheckDetailOverride cds
INNER JOIN dbo.CheckDetails cd
    ON cd.CheckDetailID = cds.CheckDetailID
OUTER APPLY CONSTANTS
