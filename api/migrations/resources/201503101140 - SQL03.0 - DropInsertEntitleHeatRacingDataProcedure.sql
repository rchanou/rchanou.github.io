USE ClubspeedV8;
SET XACT_ABORT ON;
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.ROUTINES r
    WHERE 
            r.ROUTINE_TYPE      = 'PROCEDURE'
        AND r.ROUTINE_SCHEMA    = 'dbo'
        AND r.ROUTINE_NAME      = 'InsertEntitleHeatRacingData'
)
BEGIN
    DROP PROCEDURE [dbo].[InsertEntitleHeatRacingData]
END

COMMIT;