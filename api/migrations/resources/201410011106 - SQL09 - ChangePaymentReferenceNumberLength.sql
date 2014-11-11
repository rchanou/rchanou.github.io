USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'ReferenceNumber'
        AND c.TABLE_NAME = 'PAYMENT'
        AND c.CHARACTER_MAXIMUM_LENGTH < 255
)
BEGIN
    ALTER TABLE dbo.[PAYMENT]
    ALTER COLUMN [ReferenceNumber] nvarchar(255)
END;

COMMIT;