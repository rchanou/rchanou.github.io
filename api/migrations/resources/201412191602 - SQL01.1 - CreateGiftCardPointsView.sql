CREATE VIEW [dbo].[GiftCardPoints_V] AS
WITH
GiftCardPoints AS (
    SELECT
        ph.CustID
        , SUM(ISNULL(ph.PointAmount, 0)) AS Points
    FROM dbo.PointHistory ph
    WHERE
        ph.Type != 6 -- "Cancel Point Used By Racing"
        AND ph.Type != 9 -- "Void Buy Point Item"
    GROUP BY ph.CustID
)
SELECT
    c.CustID
    , c.CrdID
    , gcp.Points
FROM GiftCardPoints gcp
INNER JOIN dbo.Customers c
    ON c.CustID = gcp.CustID
