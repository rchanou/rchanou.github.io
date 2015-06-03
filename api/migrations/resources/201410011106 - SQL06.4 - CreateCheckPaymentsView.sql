-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[CheckPayments_V] AS
SELECT
      p.CheckID
    , SUM(ISNULL(p.PayAmount, 0)) AS PaidTotal
    , SUM(ISNULL(p.PayTax, 0)) AS PaidTax
FROM dbo.Payment p
WHERE p.PayStatus != 2
GROUP BY p.CheckID