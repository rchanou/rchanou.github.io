-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[GiftCardBalance_V] AS
SELECT
      c.CrdID
    , c.CustID
    , c.IsGiftCard
    , ISNULL(gcmv.[Money], 0) AS [Money]
    , ISNULL(gcpv.Points, 0) AS Points
FROM dbo.Customers c
LEFT OUTER JOIN dbo.GiftCardMoney_V gcmv
    ON c.CustID = gcmv.CustID
LEFT OUTER JOIN dbo.GiftCardPoints_V gcpv
    ON c.CustID = gcpv.CustID
