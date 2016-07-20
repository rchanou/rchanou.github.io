CREATE VIEW [dbo].[PointBalance_V] AS
WITH
PointBalance AS (
    SELECT
        ph.CustID,
        SUM(ISNULL(ph.PointAmount, 0)) AS Balance
    FROM dbo.PointHistory ph
    WHERE
        ph.Type != 6 -- "Cancel Point Used By Racing"
        AND ph.Type != 9 -- "Void Buy Point Item"
    GROUP BY ph.CustID 
)
SELECT
    c.CustID,
    c.CrdID,
    pb.Balance
FROM PointBalance pb
INNER JOIN dbo.Customers c
    ON c.CustID = pb.CustID
