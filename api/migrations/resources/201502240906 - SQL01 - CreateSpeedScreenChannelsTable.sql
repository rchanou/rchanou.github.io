USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.SpeedScreenChannels', 'U') IS NOT NULL)
    DROP TABLE dbo.SpeedScreenChannels

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'SpeedScreenChannels'
)
BEGIN
    CREATE TABLE dbo.SpeedScreenChannels (
          [ChannelID]       INT IDENTITY(1,1)   NOT NULL
        , [ChannelNumber]   INT UNIQUE          NOT NULL
        , [ChannelData]     NVARCHAR(MAX)       NOT NULL
        , [Created]         DATETIME            DEFAULT (GETDATE())
        , CONSTRAINT        PK_Channels         PRIMARY KEY CLUSTERED (ChannelID)
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The private Channel ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = SpeedScreenChannels
        , @level2type = 'Column', @level2name = [ChannelID]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The public Channel Number'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = SpeedScreenChannels
        , @level2type = 'Column', @level2name = [ChannelNumber]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The JSON data for the Channel'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = SpeedScreenChannels
        , @level2type = 'Column', @level2name = [ChannelData]
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp for when the Channel was created'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = SpeedScreenChannels
        , @level2type = 'Column', @level2name = [Created]
END;

COMMIT;