USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

-- Drop and recreate the view by default
IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.VIEWS v
    WHERE
            v.TABLE_SCHEMA  = 'dbo'
        AND v.TABLE_NAME    = 'OnlineBookingAvailabilityPublic_V'
)
BEGIN
    DROP VIEW dbo.OnlineBookingAvailabilityPublic_V
END

CREATE VIEW dbo.OnlineBookingAvailabilityPublic_V AS
SELECT obav.*
FROM dbo.OnlineBookingAvailability_V obav
WHERE
    (
        SELECT TOP 1
        DATEADD(
            SECOND
            , CONVERT(INT, COALESCE(SettingValue, DefaultSetting, 1800))
            , SYSDATETIME()
        )
        FROM dbo.ControlPanel cp
        WHERE
            cp.TerminalName = 'Booking' 
            AND cp.SettingName = 'bookingAvailabilityWindowBeginningInSeconds'
    ) <= obav.HeatStartsAt
    AND 
    (
        SELECT TOP 1
        DATEADD(
            SECOND
            , CONVERT(INT, COALESCE(SettingValue, DefaultSetting, 1800))
            , SYSDATETIME()
        )
        FROM dbo.ControlPanel cp
        WHERE
            cp.TerminalName = 'Booking' 
            AND cp.SettingName = 'bookingAvailabilityWindowEndInSeconds'
    ) > obav.HeatStartsAt

GO

COMMIT;