USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.OnlineBookingReservations', 'U') IS NOT NULL)
    DROP TABLE dbo.OnlineBookingReservations

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'OnlineBookingReservations'
)
BEGIN
    CREATE TABLE dbo.OnlineBookingReservations (
        OnlineBookingReservationsID INT IDENTITY(1,1) NOT NULL
        , OnlineBookingsID INT NOT NULL
        , CustomersID INT -- allow to be nullable
        , SessionID NVARCHAR(255) NOT NULL
        , Quantity INT NOT NULL
        , CreatedAt DATETIME NOT NULL DEFAULT GETDATE()
        , ExpiresAt DATETIME NOT NULL DEFAULT DATEADD(MINUTE, 30, GETDATE())
        , CONSTRAINT PK_OnlineBookingReservations PRIMARY KEY CLUSTERED (OnlineBookingReservationsID)
        , CONSTRAINT FK_OnlineBookingReservations_OnlineBookings FOREIGN KEY (OnlineBookingsID)
            REFERENCES dbo.OnlineBookings (OnlineBookingsID)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        , CONSTRAINT FK_OnlineBookingReservations_Customers FOREIGN KEY (CustomersID)
            REFERENCES dbo.Customers (CustID)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The OnlineBookingReservations ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = OnlineBookingReservationsID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID for the parent OnlineBookings record'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = OnlineBookingsID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID for the customer making the reservation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = CustomersID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID for the client session making the reservation'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = SessionID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The quantity of the online booking product being reserved'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = Quantity
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the reservation was created'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = CreatedAt
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the reservation is set to expire'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservations
        , @level2type = 'Column', @level2name = ExpiresAt

END;

COMMIT;