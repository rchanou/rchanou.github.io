USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'ExternalSystemID'
        AND c.TABLE_NAME = 'EventReservations'
)
BEGIN
    ALTER TABLE [EventReservations]
    ADD [ExternalSystemID] NVARCHAR(MAX);
END;

COMMIT;
