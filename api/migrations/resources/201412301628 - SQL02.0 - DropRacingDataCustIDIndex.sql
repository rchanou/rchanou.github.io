USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM sys.indexes i
    WHERE
            i.object_id = OBJECT_ID('RacingData')
        AND i.name      = 'IX_RacingData_CustID'
)
BEGIN
    DROP INDEX IX_RacingData_CustID ON dbo.RacingData
END

COMMIT;