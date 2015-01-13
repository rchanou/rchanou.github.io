USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

CREATE NONCLUSTERED INDEX [IX_RacingData_CustID]
ON [dbo].[RacingData] (
    [CustID] ASC
)

COMMIT;