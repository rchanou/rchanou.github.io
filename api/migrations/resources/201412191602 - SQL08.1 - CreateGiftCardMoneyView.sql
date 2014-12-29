-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[GiftCardMoney_V] AS
WITH
GiftCardMoney AS (
    SELECT
        gch.CustID
        , SUM(ISNULL(gch.Points, 0)) AS [Money]
    FROM dbo.GiftCardHistory gch
    GROUP BY gch.CustID 
)
SELECT
    gcb.CustID
    , c.CrdID
    , gcb.[Money]
FROM GiftCardMoney gcb
INNER JOIN dbo.Customers c
    ON c.CustID = gcb.CustID
WHERE c.CrdID > -1