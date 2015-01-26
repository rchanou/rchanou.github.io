USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE
            c.TABLE_NAME   = 'AuthenticationTokens'
        AND c.TABLE_SCHEMA = 'dbo'
        AND c.COLUMN_NAME  = 'CustomersID'
        AND c.IS_NULLABLE  = 'NO'
)
BEGIN
    ALTER TABLE dbo.AuthenticationTokens
    ALTER COLUMN CustomersID INT NULL;
END;

COMMIT;