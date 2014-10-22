USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

-- Drop and recreate the view by default
IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.VIEWS v
    WHERE
            v.TABLE_SCHEMA  = 'dbo'
        AND v.TABLE_NAME    = 'PrimaryCustomers_V'
)
BEGIN
    DROP VIEW dbo.PrimaryCustomers_V
END

COMMIT;