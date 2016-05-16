-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[GiftCardPoints_V] AS
WITH
GiftCardPoints AS (
    SELECT
        ph.CustID
        , SUM(ISNULL(ph.PointAmount, 0)) AS Points
    FROM dbo.PointHistory ph
    GROUP BY ph.CustID
)
SELECT
    c.CustID
    , c.CrdID
    , gcp.Points
FROM GiftCardPoints gcp
INNER JOIN dbo.Customers c
    ON c.CustID = gcp.CustID
