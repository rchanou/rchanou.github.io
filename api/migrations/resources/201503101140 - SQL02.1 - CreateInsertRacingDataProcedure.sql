CREATE PROCEDURE [dbo].[InsertRacingData]
    @CustID int,
    @HeatNo int,
    @AutoNo int,
    @LapNum int,
    @LapTime int,
    @AMBTime bigint,
    @TimeStamp datetime,
    @ActualDate smalldatetime
AS
BEGIN
    INSERT INTO RacingData (CustID, HeatNo, AutoNo, LapNum, LTime, AMBTime, TimeStamp,ActualDate) 
    VALUES (@CustID, @HeatNo, @AutoNo, @LapNum, @LapTime, @AMBTime, @TimeStamp, @ActualDate)
END;