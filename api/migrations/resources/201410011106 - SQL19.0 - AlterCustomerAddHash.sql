USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'Hash'
        AND c.TABLE_NAME = 'CUSTOMERS'
)
BEGIN
    ALTER TABLE [CUSTOMERS]
    ADD [Hash] NVARCHAR(255);
END;

COMMIT;