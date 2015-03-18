-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[CheckTotals_V] AS
WITH
CONSTANTS1 AS (
    -- note these shenanigans:
    -- max is used to ensure that at least one row is returned, even if DiscountBeforeTaxes is missing (this could be potentially dangerous, if multiple settings are found with the same SettingName)
    -- isnull is used to cast null values to 0, when DiscountBeforeTaxes is not a setting in the database
    -- final cast is used, in case of 'true' or 'false' as the setting value
    SELECT TOP 1
        CAST(ISNULL(MAX(cp.SettingValue), 0) AS BIT) AS DISCOUNT_BEFORE_TAXES
    FROM dbo.ControlPanel cp
    WHERE cp.SettingName LIKE '%DiscountBeforeTaxes%'
)
, CONSTANTS2 AS (
    -- separate so we are absolutely sure we don't accidentally lose UseSalesTax
    -- from a potentially missing DiscountBeforeTaxes setting
    SELECT
        CAST(dbo.UseSalesTax() AS BIT) AS USE_SALES_TAX
)
, CONSTANTS AS (
    -- build constants into a single subquery for performance reasons.
    -- approximately 1.75x faster than re-selecting DISCOUNT_BEFORE_TAXES
    -- and dbo.UseSalesTax() for each row inside a multi-row query.
    SELECT TOP 1 -- there should only ever be 1 record.
          ISNULL(c1.DISCOUNT_BEFORE_TAXES, 0) AS DISCOUNT_BEFORE_TAXES
        , ISNULL(c2.USE_SALES_TAX, 0) AS USE_SALES_TAX
    FROM CONSTANTS2 c2
    OUTER APPLY CONSTANTS1 c1 -- outer apply c1, not the other way around, or we'll just have the same problem
)
, CheckDetailSums1 AS (
    SELECT
          cd.CheckDetailID
        , cd.CheckID
        , cd.UnitPrice
        , cd.Qty + cd.CadetQty AS CheckDetailActualQuantity
        , cd.DiscountApplied
        , (
            ISNULL(
                (((1 + cd.GST / 100) * (1 + (cd.TaxPercent - cd.GST) / 100)) - 1) * 100
                , 0
            ) / 100.00
        ) AS CheckDetailTaxPercentage
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
        , cds.UnitPrice - ISNULL(cds.DiscountApplied,0) / CheckDetailActualQuantity  AS UnitPriceAfterDiscount
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
        , ROUND(cds.UnitPriceAfterDiscount * cds.CheckDetailTaxPercentage, 2) AS CheckDetailSingleTaxAmount
    FROM CheckDetailSums2 cds
)
, CheckDetailSums4 AS (
    SELECT
          cds.CheckDetailID
        , cds.CheckID
        , cds.DiscountApplied
        , cds.CheckDetailSingleTaxAmount * cds.CheckDetailActualQuantity AS CheckDetailTax
        , cds.UnitPriceAfterDiscount * cds.CheckDetailActualQuantity AS CheckDetailSubtotal
    FROM CheckdetailSums3 cds
)
, CheckDetailSums5 AS (
    SELECT
          cds.CheckDetailID
        , cds.CheckID
        , cds.DiscountApplied
        , cds.CheckDetailTax
        , cds.CheckDetailSubtotal
        , (
            cds.CheckDetailSubtotal
            + (
                CASE WHEN CONSTANTS.USE_SALES_TAX = 1 THEN cds.CheckDetailTax
                ELSE 0 END
            ) 
        ) AS CheckDetailTotal
    FROM CheckDetailSums4 cds
    OUTER APPLY CONSTANTS
)
, CheckSums1 AS (
    SELECT
          cds.CheckID
        , SUM(cds.CheckDetailTax) AS CheckTax
        , SUM(cds.CheckDetailSubtotal)  AS CheckSubtotal --- SUM(cds.DiscountApplied)
    FROM CheckDetailSums5 cds
    INNER JOIN dbo.Checks c
        ON c.CheckID = cds.CheckID
    GROUP BY cds.CheckID
)
, ExistingPayments AS (
    SELECT
          p.CheckID
        , SUM(ISNULL(p.PayAmount, 0)) AS PaidAmount
        , SUM(ISNULL(p.PayTax, 0)) AS PaidTax
    FROM dbo.Payment p
    WHERE p.PayStatus = 1 -- PayStatus.PAID (2 is PayStatus.VOID)
    GROUP BY p.CheckID
)
, CheckTotals AS (
    SELECT
          c.CheckID
        , c.CustID
        , c.CheckType
        , c.CheckStatus
        , c.CheckName
        , c.UserID
        , c.CheckTotal as CheckTotalApplied
        , c.BrokerName
        , c.Notes
        , c.Gratuity
        , c.Fee
        , c.OpenedDate
        , c.ClosedDate
        , c.IsTaxExempt
        , c.Discount -- note: this will always be a dollar amount at this point
        --, c.DiscountID
        --, c.DiscountNotes
        --, c.DiscountUserID
        , c.InvoiceDate
        -- ISNULL the following items, in case there are no non-voided CheckDetails to join
        , ISNULL(cs.CheckSubtotal, 0) AS CheckSubtotal
        , ISNULL(cs.CheckTax, 0) AS CheckTax
        , ISNULL(cs.CheckSubtotal + CASE WHEN CONSTANTS.USE_SALES_TAX = 1 THEN cs.CheckTax ELSE 0 END  + c.Fee + c.Gratuity - c.Discount , 0) AS CheckTotal
        , ISNULL(ep.PaidTax, 0) AS CheckPaidTax -- when no payments exist, use 0
        , ISNULL(ep.PaidAmount, 0) AS CheckPaidTotal -- when no payments exist, use 0
        , cd.CheckDetailID
        , cd.Status AS CheckDetailStatus
        , cd.Type AS CheckDetailType
        , cd.ProductID
        , cd.ProductName
        , cd.CreatedDate
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
        -- ISNULL the following items, in case there are no non-voided CheckDetails to join
        , ISNULL(cds.CheckDetailTax, 0) AS CheckDetailTax
        , ISNULL(cds.CheckDetailSubtotal, 0) AS CheckDetailSubtotal
        , ISNULL(cds.CheckDetailTotal, 0) AS CheckDetailTotal
    FROM dbo.Checks c
    LEFT OUTER JOIN CheckDetailSums5 cds
        ON c.CheckID = cds.CheckID
    LEFT OUTER JOIN CheckSums1 cs
        ON c.CheckID = cs.CheckID
    LEFT OUTER JOIN dbo.CheckDetails cd
        ON cd.CheckDetailID = cds.CheckDetailID
    LEFT OUTER JOIN ExistingPayments ep
        ON ep.CheckID = c.CheckID
    OUTER APPLY CONSTANTS
)
SELECT
    c.CheckID
    , c.CustID
    , c.CheckType
    , c.CheckStatus
    , c.CheckName
    , c.UserID
    , c.CheckTotalApplied -- stored on the Check record
    , c.BrokerName
    , c.Notes
    , c.Gratuity
    , c.Fee
    , c.OpenedDate
    , c.ClosedDate
    , c.IsTaxExempt
    , c.Discount
    , c.InvoiceDate
    , c.CheckTax -- calculated from aggregated check details
    , c.CheckSubTotal -- calculated aggregated from check details
    , c.CheckTotal -- calculated aggregated from check details
    , c.CheckPaidTax -- calculated aggregated from payments
    , c.CheckPaidTotal -- calculated aggregated from payments
    , c.CheckTax - c.CheckPaidTax AS CheckRemainingTax
    , c.CheckTotal - c.CheckPaidTotal AS CheckRemainingTotal
    , c.CheckDetailID
    , c.CheckDetailStatus
    , c.CheckDetailType
    , c.ProductID
    , c.ProductName
    , c.CreatedDate
    , c.Qty
    , c.UnitPrice
    , c.UnitPrice2
    , c.DiscountApplied
    , c.TaxID
    , c.TaxPercent
    , c.VoidNotes
    , c.CID
    , c.VID
    , c.BonusValue
    , c.PaidValue
    , c.ComValue
    , c.Entitle1
    , c.Entitle2
    , c.Entitle3
    , c.Entitle4
    , c.Entitle5
    , c.Entitle6
    , c.Entitle7
    , c.Entitle8
    , c.M_Points
    , c.M_CustID
    , c.M_OldMembershiptypeID
    , c.M_NewMembershiptypeID
    , c.M_Days
    , c.M_PrimaryMembership
    , c.P_PointTypeID
    , c.P_Points
    , c.P_CustID
    , c.R_Points
    , c.DiscountUserID
    , c.DiscountDesc
    , c.CalculateType
    , c.DiscountID
    , c.DiscountNotes
    , c.G_Points
    , c.G_CustID
    , c.GST
    , c.M_DaysAdded
    , c.S_SaleBy
    , c.S_NoOfLapsOrSeconds
    , c.S_CustID
    , c.S_Vol
    , c.CadetQty
    , c.CheckDetailTax -- calculated from check details
    , c.CheckDetailSubtotal -- calculated from check details
    , c.CheckDetailTotal -- calculated from check details
FROM CheckTotals c