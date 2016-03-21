-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[CheckTotals_V] AS
SELECT
      c.CheckID
    , c.CustID
    , c.CheckType
    , c.CheckStatus
    , c.CheckName
    , c.UserID
    , c.CheckTotalApplied
    , c.BrokerName
    , c.Notes
    , c.Gratuity
    , c.Fee
    , c.OpenedDate
    , c.ClosedDate
    , c.IsTaxExempt
    , c.Discount
    , c.CheckDiscountID
    , c.CheckDiscountNotes
    , c.CheckDiscountUserID
    , c.CheckSubtotal
    , c.CheckTax
    , c.CheckGST
    , c.CheckPST
    , c.CheckTotal
    , c.CheckPaidTax
    , c.CheckPaidTotal
    , c.CheckRemainingTax
    , c.CheckRemainingTotal
    , cd.CheckDetailID
    , cd.CheckDetailStatus
    , cd.CheckDetailType
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
    , cd.DiscountUserID AS CheckDetailDiscountUserID
    , cd.DiscountDesc AS CheckDetailDiscountDesc
    , cd.CalculateType AS CheckDetailDiscountCalculateType
    , cd.DiscountID AS CheckDetailDiscountID
    , cd.DiscountNotes AS CheckDetailDiscountNotes
    , cd.G_Points
    , cd.G_CustID
    , cd.GST
    , cd.M_DaysAdded
    , cd.S_SaleBy
    , cd.S_NoOfLapsOrSeconds
    , cd.S_CustID
    , cd.S_Vol
    , cd.CadetQty
    , ISNULL(cd.CheckDetailSubtotal, 0) AS CheckDetailSubtotal -- these need to be ISNULLed again - left outer join has a reasonably high chance of having no match
    , ISNULL(cd.CheckDetailTax, 0) AS CheckDetailTax
    , ISNULL(cd.CheckDetailGST, 0) AS CheckDetailGST
    , ISNULL(cd.CheckDetailPST, 0) AS CheckDetailPST
    , ISNULL(cd.CheckDetailTotal, 0) AS CheckDetailTotal
FROM dbo.Checks_V c
LEFT OUTER JOIN dbo.CheckDetails_V cd
    ON c.CheckID = cd.CheckID