USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

-- index should be re-built by default
CREATE NONCLUSTERED INDEX [IX_RacingData_HeatNo] ON [dbo].[RacingData] (
    [HeatNo] ASC
)

COMMIT;