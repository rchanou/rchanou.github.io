USE ClubspeedV8;
SET XACT_ABORT ON;
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
BEGIN TRANSACTION;

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
        AND c.COLUMN_NAME = 'CheckID'
    )
    BEGIN
        ALTER TABLE dbo.OnlineBookingReservations
        ADD CheckID INT

        ALTER TABLE dbo.OnlineBookingReservations
        ADD CONSTRAINT FK_OnlineBookingReservations_Checks
            FOREIGN KEY (CheckID)
            REFERENCES dbo.Checks (CheckID)
            ON DELETE NO ACTION
            ON UPDATE CASCADE

        EXEC sp_addextendedproperty
              @name = 'MS_Description'
            , @value = 'The ID for the Check record'
            , @level0type = 'Schema', @level0name = dbo
            , @level1type = 'Table',  @level1name = OnlineBookingReservations
            , @level2type = 'Column', @level2name = CheckID
    END;
END;

COMMIT;