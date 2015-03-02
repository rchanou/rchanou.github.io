CREATE PROCEDURE [dbo].[FillHeatRange]
      @StartDate DATE = NULL    -- the date from which to start filling. defaults to today.
    , @EndDate DATE = NULL      -- the date at which to stop filling. this date is inclusive, and defaults to {current_year}-12-31.
    , @StartTime TIME           -- the opening time for the track
    , @EndTime TIME             -- the closing time for the track (can be after midnight)
    , @HeatType INT             -- the id of the heat type to use
    , @HeatColor INT = -2302756 -- a default, readable color for the interface
AS
BEGIN
/*
    dbo.FillHeatRange

    To be used to fill heats for a given day range,
    between a given track open and close range,
    based on a provided heat type.
*/
IF (@StartDate IS NULL)
    SET @StartDate = CAST(GETDATE() AS DATE) -- use the beginning of today
IF (@EndDate IS NULL)
    SET @EndDate = CAST(DATEADD(yy, DATEDIFF(yy, 0, GETDATE()) + 1, -1) AS DATE)

-- Internal variables
DECLARE @Duration INT;
DECLARE @RecursiveEndTime TIME;
DECLARE @ActualEndDate DATE;
DECLARE @StartDateTime DATETIME;
DECLARE @EndDateTime DATETIME;

-- Grab the duration from HeatTypes
SET @Duration = (
    SELECT TOP 1
        ht.ScheduleDuration
    FROM
        dbo.HeatTypes ht
    WHERE
        ht.HeatTypeNo = @HeatType
);
IF (@Duration IS NULL)
    RAISERROR('Heat Duration was NULL! Check that the entered HeatType is valid!', 16, 1);

-- We want to stop BEFORE @EndTime, not include it,
-- and we want to make sure we aren't scheduling races
-- which will go past the @EndTime for the track.
-- As such, we need to step back to two "durations" behind the @EndTime,
-- as the recursive cte will already include one "duration" behind the @EndTime.
SET @RecursiveEndTime = CAST(DATEADD(MINUTE, @Duration * -2, @EndTime) AS TIME); 

-- Set the actual end date.
-- This will be one day beyond the given end date,
-- if the track closes past midnight.
SET @ActualEndDate = 
    CASE
        WHEN @StartTime > @EndTime THEN CAST(DATEADD(DAY, 1, @EndDate) AS DATE)
        ELSE @EndDate
    END;

SET @StartDateTime = (CAST(@StartDate AS DATETIME) + CAST(@StartTime AS DATETIME));
SET @EndDateTime = (CAST(@ActualEndDate AS DATETIME) + CAST(@EndTime AS DATETIME));

;WITH TIMES_CTE AS (
    SELECT
        @StartTime 'Time'
        -- consider adding a rank, if we ever need to alternate between multiple heat types.
    UNION ALL
    SELECT 
        CAST(DATEADD(MINUTE, @Duration, CAST(t.[Time] AS DATETIME)) AS TIME)
    FROM TIMES_CTE t
    WHERE
        -- start time is less than endtime, handle like a sane person.
        (@StartTime < @RecursiveEndTime AND (t.[Time] >= @StartTime AND t.[Time] <= @RecursiveEndTime))
        OR
        -- start time is greater than end time (so end time is past or at midnight),
        -- so we need to continue going past midnight (00) until we hit the endtime.
        (@StartTime > @RecursiveEndTime AND (t.[Time] >= @StartTime OR t.[Time] <= @RecursiveEndTime))
)
, DAYS_CTE AS (
    SELECT
        @StartDate 'Date'
        -- consider adding a rank, if we ever need to alternate between multiple heat types.
    UNION ALL
    SELECT 
        CAST(DATEADD(DAY, 1, CAST(d.[Date] AS DATETIME)) AS DATE)
    FROM DAYS_CTE d
    WHERE
        d.[Date] < @ActualEndDate
)
, DATETIMES_CTE AS (
    SELECT
        CAST(d.[Date] AS DATETIME) + CAST(t.[Time] AS DATETIME) AS 'ScheduledTime'
    FROM TIMES_CTE t
    CROSS JOIN DAYS_CTE d
)
INSERT INTO dbo.HeatMain(
    TrackNo
    , Scheduledtime
    , HeatTypeNo
    , LapsOrMinutes
    , HeatStatus
    , EventRound
    , Begining
    , Finish
    , WinBy
    , RaceBy
    , ScheduleDuration
    , PointsNeeded
    , SpeedLevel
    , HeatColor
    , NumberOfReservation
    , MemberOnly
    , HeatNotes
    , ScoreID
    , RacersPerHeat
    , NumberOfCadetReservation
    , CadetsPerHeat
)
SELECT
    Trackno
    , dt.ScheduledTime
    , HeatTypeNo
    , LapsOrMinutes
    , 0 -- HeatStatus
    , NULL -- EventRound
    , NULL -- Begining
    , NULL -- Finish
    , WinBy
    , RaceBy
    , ScheduleDuration
    , Cost
    , SpeedLevel
    , @HeatColor
    , 0 -- NumberOfReservation
    , 0 -- MemberOnly
    , '' -- HeatNotes
    , 0 -- ScoreID
    , RacersPerHeat
    , 0 -- NumberOfCadetReservation
    , CadetsPerHeat
FROM dbo.HeatTypes ht
CROSS JOIN DATETIMES_CTE dt
WHERE
        ht.HeatTypeNo = @HeatType
    AND dt.ScheduledTime >= @StartDateTime -- chop off beginning 'bookend'
    AND dt.ScheduledTime < @EndDateTime -- chop off ending 'bookend'
      -- note that those 'bookends' will be issues when @StartTime > @EndTime.
      -- we could theoretically filter these out earlier by adding additional CTEs above,
      -- but this should be efficient enough.
OPTION(MAXRECURSION 32767); -- shouldn't need nearly this much, but just in case.

END;