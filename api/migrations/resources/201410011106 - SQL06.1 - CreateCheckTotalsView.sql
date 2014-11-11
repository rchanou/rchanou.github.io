-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[CheckTotals_V] AS
WITH
CheckDetailSums1 AS (
    SELECT
        cd.CheckDetailID
        , cd.CheckID
        , ISNULL(
            (
                Round(ISNULL((UnitPrice * (Qty + CadetQty) - DiscountApplied) , 0),2) 
            )
            , 0
        ) AS CheckDetailSubtotal
        , ISNULL(
            (
                Round(ISNULL((UnitPrice * (Qty + CadetQty) - DiscountApplied) * (CASE WHEN dbo.UseSalesTax() = 1 THEN (((1 + GST / 100) * (1 + (TaxPercent - GST) / 100)) - 1) * 100 ELSE 0 END) / 100.00, 0),2)
            )
            , 0
        ) AS CheckDetailTax
    FROM dbo.CheckDetails cd
    WHERE cd.Status <> 2
)
, CheckDetailSums2 AS (
    SELECT
        cds1.CheckDetailID
        , cds1.CheckID
        , cds1.CheckDetailTax
        , cds1.CheckDetailSubtotal
        , ISNULL(cds1.CheckDetailSubtotal + cds1.CheckDetailTax, 0) AS CheckDetailTotal
    FROM CheckDetailSums1 cds1
)
, CheckSums AS (
    -- all logic taken from existing stored proc: dbo.ApplyCheckTotal
    SELECT
        cds2.CheckID
        , SUM(cds2.CheckDetailTax) AS CheckTax
        , SUM(cds2.CheckDetailSubtotal) AS CheckSubtotal
        , SUM(cds2.CheckDetailTotal) AS CheckTotalTemp
    FROM CheckDetailSums2 cds2
    GROUP BY cds2.CheckID
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
        , c.Discount
        --, c.DiscountID
        --, c.DiscountNotes
        --, c.DiscountUserID
        , c.InvoiceDate
        , cs.CheckSubtotal
        , cs.CheckTax
        , cs.CheckTotalTemp + c.Gratuity + c.Fee - c.Discount AS CheckTotal
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
        , cds2.CheckDetailTax
        , cds2.CheckDetailSubTotal
        , cds2.CheckDetailTotal
        , ISNULL(ep.PaidTax, 0) AS PaidTax -- when no payments exist, use 0
    FROM dbo.Checks c
    LEFT OUTER JOIN CheckDetailSums2 cds2
        ON c.CheckID = cds2.CheckID
    LEFT OUTER JOIN CheckSums cs
        ON c.CheckID = cs.CheckID
    LEFT OUTER JOIN dbo.CheckDetails cd
        ON cd.CheckDetailID = cds2.CheckDetailID
    LEFT OUTER JOIN ExistingPayments ep
        ON ep.CheckID = c.CheckID
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
    , c.CheckTax -- calculated
    , c.CheckSubTotal -- calculated
    , c.CheckTotal -- calculated
    , c.CheckPaidTax -- calculated from payments
    , c.CheckPaidTotal -- calculated from payments
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
    , c.CheckDetailTax -- calculated
    , c.CheckDetailSubtotal -- calculated
    , c.CheckDetailTotal -- calculated
FROM CheckTotals c