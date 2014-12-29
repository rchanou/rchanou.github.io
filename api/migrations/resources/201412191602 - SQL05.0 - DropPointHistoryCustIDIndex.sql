USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM sys.indexes i
    WHERE
            i.object_id = OBJECT_ID('PointHistory')
        AND i.name      = 'IX_PointHistory_CustID'
)
BEGIN
    DROP INDEX IX_PointHistory_CustID ON dbo.PointHistory
END

COMMIT;