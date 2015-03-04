USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'Hash'
        AND c.TABLE_NAME = 'CUSTOMERS'
)
BEGIN
    UPDATE c
    SET
        c.Hash = c.Password
    FROM dbo.Customers c
    WHERE
            c.Password IS NOT NULL
        AND LEN(LTRIM(RTRIM(c.Password))) > 0
        AND c.Hash IS NULL
END;

COMMIT;