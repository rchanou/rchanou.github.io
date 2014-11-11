USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.OnlineBookingReservationStatus', 'U') IS NOT NULL)
    DROP TABLE dbo.OnlineBookingReservationStatus

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'OnlineBookingReservationStatus'
)
BEGIN
    CREATE TABLE dbo.OnlineBookingReservationStatus (
        OnlineBookingReservationStatusID INT IDENTITY(1,1) NOT NULL
        , [Status] NVARCHAR(255) NOT NULL
        , CONSTRAINT PK_OnlineBookingReservationStatus PRIMARY KEY CLUSTERED (OnlineBookingReservationStatusID)
		, CONSTRAINT UQ_OnlineBookingReservationStatus_Status UNIQUE NONCLUSTERED ([Status]) -- create the unique index this way, for naming convention purposes
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The OnlineBookingReservationStatus ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservationStatus
        , @level2type = 'Column', @level2name = OnlineBookingReservationStatusID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The status definition'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = OnlineBookingReservationStatus
        , @level2type = 'Column', @level2name = [Status]

	INSERT INTO dbo.OnlineBookingReservationStatus(Status)
	VALUES
	      ('TEMPORARY')
	    , ('PERMANENT')
END;

COMMIT;