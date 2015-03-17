-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch
CREATE VIEW [dbo].[ActiveRaceLapCount_V] AS
SELECT
      hm.TrackNo
    , rd.HeatNo
    , COUNT(rd.ID) AS LapCount
FROM dbo.HeatMain hm
INNER JOIN dbo.RacingData rd
    ON rd.HeatNo = hm.HeatNo
WHERE
    hm.HeatStatus = 1 -- Race currently running
GROUP BY
      hm.TrackNo
    , rd.HeatNo;