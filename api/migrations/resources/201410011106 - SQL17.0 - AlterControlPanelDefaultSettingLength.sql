USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'DefaultSetting'
        AND c.TABLE_NAME = 'ControlPanel'
        AND (
                c.CHARACTER_MAXIMUM_LENGTH <> -1 -- signifies MAX in information schemas
            OR  c.DATA_TYPE <> 'nvarchar'
        )
)
BEGIN
    ALTER TABLE [ControlPanel]
    ALTER COLUMN [DefaultSetting] NVARCHAR(MAX)
END;

COMMIT;