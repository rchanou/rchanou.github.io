CREATE PROCEDURE [dbo].[InsertEntitleHeatRacingData]
    @CustID int,
    @HeatNo int,
    @AutoNo int,
    @LapTime int,
    @AMBTime bigint,
    @TimeStamp datetime,
    @ActualDate smalldatetime
AS
BEGIN
    INSERT INTO RacingData (CustID, HeatNo, AutoNo, LapNum, LTime, AMBTime, TimeStamp, ActualDate) 
    SELECT TOP 1 @CustID, @HeatNo, @AutoNo, LapNum + 1, @LapTime, @AMBTime, @TimeStamp,@ActualDate 
    FROM dbo.RacingData 
    WHERE CustId = @CustID AND HeatNo=@HeatNo 
    ORDER BY LapNum DESC
END