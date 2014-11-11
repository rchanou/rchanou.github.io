USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.OnlineBookings', 'U') IS NOT NULL)
    DROP TABLE dbo.OnlineBookings

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'OnlineBookings'
)
BEGIN
    CREATE TABLE dbo.OnlineBookings (
        OnlineBookingsID INT IDENTITY(1,1) NOT NULL
        , HeatMainID INT NOT NULL
        , ProductsID INT NOT NULL
        , IsPublic BIT NOT NULL DEFAULT 1
        , QuantityTotal INT NOT NULL
        , CONSTRAINT PK_OnlineBookings PRIMARY KEY CLUSTERED (OnlineBookingsID)
        , CONSTRAINT FK_OnlineBookings_HeatMain FOREIGN KEY (HeatMainID)
            REFERENCES dbo.HeatMain (HeatNo)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        , CONSTRAINT FK_OnlineBookings_Products FOREIGN KEY (ProductsID)
            REFERENCES dbo.Products (ProductID)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The OnlineBookings ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookings
        , @level2type = 'Column', @level2name = OnlineBookingsID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID from dbo.HeatMain for the online booking'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookings
        , @level2type = 'Column', @level2name = HeatMainID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID from dbo.Products for the online booking'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookings
        , @level2type = 'Column', @level2name = ProductsID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The flag representing whether or not the booking should be shown in the client list or hidden behind a private url'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookings
        , @level2type = 'Column', @level2name = IsPublic
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The total quantity of event positions desired to display to the client list (which may or may not be the same number as the physically available slots)'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookings
        , @level2type = 'Column', @level2name = QuantityTotal
END;

COMMIT;