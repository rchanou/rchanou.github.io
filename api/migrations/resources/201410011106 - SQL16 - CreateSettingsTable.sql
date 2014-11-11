USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.Settings', 'U') IS NOT NULL)
    DROP TABLE dbo.Settings

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'Settings'
)
BEGIN
    CREATE TABLE dbo.Settings (
          [SettingsID]      INT IDENTITY(1,1)   NOT NULL
        , [Namespace]       NVARCHAR(255)       NOT NULL
        , [Name]            NVARCHAR(255)       NOT NULL
        , [Type]            NVARCHAR(50)        NOT NULL
        , [DefaultValue]    NVARCHAR(MAX)
        , [Value]           NVARCHAR(MAX)
        , [Description]     NVARCHAR(MAX)
        , [Created]         DATETIME            DEFAULT (GETDATE())
        , [IsPublic]        BIT                 DEFAULT (1)
        , CONSTRAINT        PK_Settings         PRIMARY KEY CLUSTERED (SettingsID)
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The Settings ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [SettingsID]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The namespace for the setting'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Namespace]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The name of the setting'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Name]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The expected data type for the value'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Type]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The default value of the setting'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [DefaultValue]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The current value of the setting'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Value]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The description of the setting'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Description]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the setting was created'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [Created]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The flag to determine whether or not the setting should be visible for editing from the application'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Settings
        , @level2type = 'Column', @level2name = [IsPublic]
END;

COMMIT;