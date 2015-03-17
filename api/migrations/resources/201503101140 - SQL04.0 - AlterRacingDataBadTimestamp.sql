USE ClubspeedV8;
SET XACT_ABORT ON;
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation while executing this one. 
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.TABLE_NAME = 'RacingDataBad'
        AND c.COLUMN_NAME = 'TimeStamp'
        AND c.DATA_TYPE != 'datetime'
)
BEGIN
    ALTER TABLE [RacingDataBad]
    ALTER COLUMN [TimeStamp] DATETIME
END;

COMMIT;