-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[GiftCardBalance_V] AS
WITH
GiftCardBalance AS (
    SELECT
        gch.CustID,
        SUM(ISNULL(gch.Points, 0)) AS Balance
    FROM dbo.GiftCardHistory gch
    GROUP BY gch.CustID 
)
SELECT
    gcb.CustID,
    c.CrdID,
    gcb.Balance
FROM GiftCardBalance gcb
INNER JOIN dbo.Customers c
    ON c.CustID = gcb.CustID