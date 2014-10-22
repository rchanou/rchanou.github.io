-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[FacebookRaces_V] AS
SELECT
      fb.CustID
    , fb.Access_Token
    , hm.HeatNo
    , ht.HeatTypeName
    , hd.FinishPosition
    , hm.Finish
FROM
    FB_Customers_New fb 
INNER JOIN dbo.HeatDetails hd
    ON fb.CustID = hd.CustID
INNER JOIN dbo.HeatMain hm
    ON hm.HeatNo = hd.HeatNo
LEFT OUTER JOIN dbo.HeatTypes ht
    ON ht.HeatTypeNo = hm.HeatTypeNo
WHERE
    hm.HeatStatus IN (2, 3)
    AND hd.FinishPosition IS NOT NULL
    AND fb.Access_token IS NOT NULL
    AND LEN(LTRIM(RTRIM(fb.Access_token))) > 0 -- remove empty strings