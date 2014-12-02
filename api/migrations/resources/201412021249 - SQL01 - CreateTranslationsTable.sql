USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.Translations', 'U') IS NOT NULL)
    DROP TABLE dbo.Settings

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'Translations'
)
BEGIN
    CREATE TABLE dbo.Translations (
          [TranslationsID]  INT IDENTITY(1,1)   NOT NULL
        , [Namespace]       NVARCHAR(50)        NOT NULL
        , [Name]            NVARCHAR(255)       NOT NULL
        , [Culture]         NVARCHAR(10)        NOT NULL
        , [DefaultValue]    NVARCHAR(MAX)
        , [Value]           NVARCHAR(MAX)
        , [Description]     NVARCHAR(MAX)
        , [Created]         DATETIME            DEFAULT (GETDATE())
        , CONSTRAINT        PK_Translations PRIMARY KEY CLUSTERED (TranslationsID)
        , CONSTRAINT        IX_Translations_Namespace_Culture_Name UNIQUE NONCLUSTERED (Namespace, Culture, Name)
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The Translations ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [TranslationsID]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The namespace for the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Namespace]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The name of the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Name]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The culture for the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Culture]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The default value of the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [DefaultValue]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The current value of the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Value]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The description of the translation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Description]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the translation was created'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = Translations
        , @level2type = 'Column', @level2name = [Created]
END;

COMMIT;