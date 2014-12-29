USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.ROUTINES r
    WHERE 
            r.ROUTINE_TYPE      = 'PROCEDURE'
        AND r.ROUTINE_SCHEMA    = 'dbo'
        AND r.ROUTINE_NAME      = 'GiftCardRegistration'
)
BEGIN
    DROP PROCEDURE dbo.GiftCardRegistration
END

COMMIT;