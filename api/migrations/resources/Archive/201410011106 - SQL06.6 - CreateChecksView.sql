-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[Checks_V] AS
WITH CheckSums1 AS (
    SELECT
          cds.CheckID
        , SUM(cds.CheckDetailTax) AS CheckTax
        , SUM(cds.CheckDetailSubtotal) AS CheckSubtotal
        , SUM(cds.CheckDetailGST) AS CheckGST
        , SUM(cds.CheckDetailPST) AS CheckPST
        , SUM(cds.CheckDetailTotal) AS CheckTotal
    FROM CheckDetails_V cds
    INNER JOIN dbo.Checks c
        ON c.CheckID = cds.CheckID
    GROUP BY cds.CheckID
)
, ChecksTemp1 AS (
    SELECT
          cs.CheckID
        , ISNULL(cs.CheckSubtotal, 0) AS CheckSubtotal
        , ISNULL(cs.CheckGST, 0) AS CheckGST
        , ISNULL(cs.CheckPST, 0) AS CheckPST
        , ISNULL(cs.CheckTax, 0) AS CheckTax
        , ISNULL(cs.CheckTotal, 0) AS CheckTotal
    FROM CheckSums1 cs
)
, ChecksTemp2 AS (
    SELECT
          ct.CheckID
        , ct.CheckSubtotal
        , ct.CheckGST
        , ct.CheckPST
        , ct.CheckTax
        , (
            ct.CheckTotal
            + c.Fee
            + c.Gratuity
            - c.Discount
        ) AS CheckTotal
    FROM ChecksTemp1 ct
    LEFT OUTER JOIN dbo.Checks c
        ON ct.CheckID = c.CheckID
)
, ChecksOverride AS (
    SELECT
          ct.CheckID
        , ROUND(ct.CheckSubtotal, 2) AS CheckSubtotal
        , ROUND(ct.CheckGST, 2) AS CheckGST
        , ROUND(ct.CheckPST, 2) AS CheckPST
        , ROUND(ct.CheckTax, 2) AS CheckTax
        , ROUND(ct.CheckTotal, 2) AS CheckTotal
    FROM ChecksTemp2 ct
    INNER JOIN dbo.Checks c
        ON c.CheckID = ct.CheckID
)
SELECT
      c.CheckID
    , c.CustID
    , ISNULL(ct.CheckSubtotal, 0) AS CheckSubtotal -- still need ISNULL at this point, since CheckDetails_V (see CheckSums1) may not have any matching non-voided records to join against
    , ISNULL(ct.CheckTax, 0) AS CheckTax
    , ISNULL(ct.CheckGST, 0) AS CheckGST
    , ISNULL(ct.CheckPST, 0) AS CheckPST
    , ISNULL(ct.CheckTotal, 0) AS CheckTotal
    , ISNULL(cpv.PaidTax, 0) AS CheckPaidTax
    , ISNULL(cpv.PaidTotal, 0) AS CheckPaidTotal
    , (ISNULL(ct.CheckTotal, 0) - ISNULL(cpv.PaidTotal, 0)) AS CheckRemainingTotal
    , (ISNULL(ct.CheckTax, 0) - ISNULL(cpv.PaidTax, 0)) AS CheckRemainingTax
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
    , c.DiscountID AS CheckDiscountID
    , c.DiscountNotes AS CheckDiscountNotes
    , c.DiscountUserID AS CheckDiscountUserID
    , c.InvoiceDate
FROM dbo.Checks c
LEFT OUTER JOIN CheckPayments_V cpv
    ON c.CheckID = cpv.CheckID
LEFT OUTER JOIN ChecksOverride ct
    ON c.CheckID = ct.CheckID
