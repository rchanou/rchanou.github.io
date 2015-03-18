USE ClubspeedV8;
SET XACT_ABORT ON;
BEGIN TRANSACTION;

-- drops index if it exists. We need to drop the index because it references the smalldatetime.
-- We will add the index back later with the new datetime data type.
IF EXISTS (
    SELECT *
    FROM sys.indexes i
    WHERE
            i.object_id = OBJECT_ID('RacingData')
        AND i.name      = 'IX_RacingData_AutoNo'
)
BEGIN
    DROP INDEX [IX_RacingData_AutoNo] ON [dbo].[RacingData]
END

COMMIT;