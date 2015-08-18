CREATE VIEW [dbo].[PointBalance_V] AS
WITH
PointBalance AS (
    SELECT
        ph.CustID,
        SUM(ISNULL(ph.PointAmount, 0)) AS Balance
    FROM dbo.PointHistory ph
    GROUP BY ph.CustID 
)
SELECT
    c.CustID,
    c.CrdID,
    pb.Balance
FROM PointBalance pb
INNER JOIN dbo.Customers c
    ON c.CustID = pb.CustID
