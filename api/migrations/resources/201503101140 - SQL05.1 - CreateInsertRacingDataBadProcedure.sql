CREATE PROCEDURE [dbo].[InsertRacingDataBad]
    @AMBNumber int,
    @AMBTime bigint,
    @TimeStamp datetime,
    @Reason nvarchar(1000)
AS
BEGIN
    INSERT INTO RacingDataBad (AMBNumber, AMBTime, TimeStamp, Reason) 
    VALUES (@AMBNumber, @AMBTime, @TimeStamp, @Reason)
END;