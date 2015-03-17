USE ClubspeedV8;
SET XACT_ABORT ON;
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.VIEWS v
    WHERE
            v.TABLE_SCHEMA  = 'dbo'
        AND v.TABLE_NAME    = 'ActiveRaceLapCount_V'
)
BEGIN
    DROP VIEW [dbo].[ActiveRaceLapCount_V]
END

COMMIT;