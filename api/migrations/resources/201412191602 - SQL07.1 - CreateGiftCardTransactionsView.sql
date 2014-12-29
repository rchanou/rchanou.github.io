CREATE VIEW dbo.GiftCardTransactions_V AS
WITH GiftCardTransactions AS (
    SELECT
          c.CrdID
        , ph.PointAmount AS Points
        , NULL AS [Money]
        , ph.PointDate AS [Date]
        , ph.Notes
    FROM dbo.Customers c
    INNER JOIN dbo.PointHistory ph
        ON c.CustID = ph.CustID
    WHERE
        c.CustID > -1

    UNION ALL

    SELECT
          c.CrdID
        , NULL AS Points
        , gch.Points AS [Money]
        , gch.TransactionDate AS [Date]
        , gch.Notes
    FROM dbo.Customers c
    INNER JOIN dbo.GiftCardHistory gch
        ON c.CustID = gch.CustID
    WHERE
        c.CustID > -1
)
SELECT
    gct.CrdID
    , gct.[Money]
    , gct.Points
    , gct.[Date]
    , gct.Notes
FROM GiftCardTransactions gct