USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.OnlineBookingReservations', 'U') IS NOT NULL)
    DROP TABLE dbo.OnlineBookingReservations

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'OnlineBookingReservations'
)
BEGIN
    IF NOT EXISTS (
        SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS c
        WHERE c.TABLE_NAME = 'OnlineBookingReservations'
        AND c.COLUMN_NAME = 'OnlineBookingReservationStatusID'
    )
    BEGIN
        ALTER TABLE dbo.OnlineBookingReservations
        ADD OnlineBookingReservationStatusID INT DEFAULT 1

        EXEC sp_executesql N'
            UPDATE obr
            SET obr.OnlineBookingReservationStatusID = (
                SELECT TOP 1 obrs.OnlineBookingReservationStatusID
                FROM dbo.OnlineBookingReservationStatus obrs
                WHERE obrs.Status = ''TEMPORARY''
            )
            FROM dbo.OnlineBookingReservations obr
            WHERE obr.OnlineBookingReservationStatusID IS NULL
        '; -- hacky, but has to be done in sp_executesql to stay inside same if statement -- batch statement will not compile correctly, as we are adding and updating a column in one shot

        ALTER TABLE dbo.OnlineBookingReservations
        ALTER COLUMN OnlineBookingReservationStatusID INT NOT NULL

        ALTER TABLE dbo.OnlineBookingReservations
        ADD CONSTRAINT FK_OnlineBookingReservations_OnlineBookingReservationStatus FOREIGN KEY (OnlineBookingReservationStatusID)
            REFERENCES dbo.OnlineBookingReservationStatus (OnlineBookingReservationStatusID)
            ON DELETE NO ACTION
            ON UPDATE CASCADE
        EXEC sp_addextendedproperty
            @name = 'MS_Description'
            , @value = 'The ID for the OnlineBookingReservationStatus record'
            , @level0type = 'Schema', @level0name = dbo
            , @level1type = 'Table',  @level1name = OnlineBookingReservations
            , @level2type = 'Column', @level2name = OnlineBookingReservationStatusID
    END;
END;

COMMIT;