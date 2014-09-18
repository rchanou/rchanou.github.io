<?php
/**
 * This script updates the GetScoreboard Stored Procedure to address
 * a bug in the sorting and displaying of position-based races.
 */

$dropProcedure = <<<EOD
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetScoreboard]') AND type in (N'P', N'PC'))

DROP PROCEDURE [dbo].[GetScoreboard]
EOD;

$createProcedure = <<<EOD
CREATE PROCEDURE [dbo].[GetScoreboard]

@TrackNo int,

@HeatNo int

As

Begin

Declare @Winby int

Declare @BLtime int

Declare @BLNum int

Declare @FirstPlace int

Declare @FirstPlaceAmbTime bigint



if @TrackNo > 0

begin

	select @HeatNo = HeatNo From Heatmain Where TrackNo = @TrackNo and HeatStatus = 1

end



Select @Winby = Winby From HeatMain Where HeatNo = @Heatno

Select @BLtime = Min(LTime) From Racingdata where LapNum > 0 And IsbadTime = 0 And Ltime > 0 And HeatNo = @HeatNo

Select @BLNum = Max(LapNum) From Racingdata where LapNum > 0 And IsbadTime = 0 And Ltime > 0 And HeatNo = @HeatNo

Select top 1 @FirstPlace = CustID, @FirstPlaceAmbTime = AmbTime From RacingData 

Where LapNum > 0 And IsbadTime = 0 And Ltime > 0 And HeatNo = @HeatNo and LapNum = @BLNum

Order by AmbTime



Select ROW_NUMBER() OVER(ORDER BY 

						 Case When @Winby = 1 Then racing.lap_num END DESC, 

						 Case When @Winby = 1 Then racing.ambtime END ASC, 

						 Case When @Winby = 0 Then racing.fastest_lap_time END ASC						

						 ) position, racing.* From (

  Select  customers.racername as nickname,

			Convert(Decimal(15,3), (cast(AVG(racingdata.LTime) as decimal(15,3)) / 1000)) average_lap_time,

			Convert(Decimal(15,3), (cast(MIN(racingdata.LTime) as decimal(15,3)) / 1000)) fastest_lap_time,

			Convert(Decimal(15,3), (cast(MIN(LatestLap.lTime) as decimal(15,3)) / 1000)) last_lap_time,

			customers.rpm,

      customers.FName first_name,

      customers.LName last_name,

			racingData.CustID racer_id,

			Max(LapNum) lap_num,

			Latestlap.AutoNo kart_num,

			Case When @Winby = 0 Then Convert(Decimal(15,3), (cast(min(racingdata.ltime) - @BLtime as decimal(15,3)) / 1000))

				 Else --The Case above was to check if winby best time 

					  --Now calculate gap for position race, will return number of laps behind or 

					  --If they are tied with the most laps then return the gap in seconds

				 Case when racingdata.custid <> @FirstPlace and @BLNum - max(lapnum) = 0 Then Convert(Decimal(15,3), (cast(max(racingdata.AmbTime) - @FirstPlaceAmbTime as decimal(15,3)) / 1000)) 

					  Else @BLNum - max(lapnum) End

				 End gap,

			Latestlap.ambtime

	From RacingData

	Inner join heatdetails on racingdata.heatno= heatdetails.heatno

	Inner Join Customers on racingdata.custid = customers.custid

	Inner Join (

		select rd.CustID, rd.Ltime, Max(rd.ambtime) ambtime, rd.autono from racingdata rd

		Inner Join (select CustID, Max(LapNum) latestlap From racingdata

		Where Heatno = @HeatNo and IsBadTime = 0 and LapNum > 0 and LTime > 0

		Group by CustID

		) latest

	on rd.custid = latest.custid

	Where Heatno = @HeatNo and rd.lapnum = latest.latestlap and IsBadTime = 0 and LapNum > 0 and LTime > 0

	Group by rd.custid, rd.ltime, rd.ambtime, rd.autono

	) LatestLap 

	

	on racingdata.CustID = latestlap.CustID

	Where heatdetails.Heatno = @Heatno and racingdata.IsBadTime = 0 and racingdata.LapNum > 0 and racingdata.LTime > 0

	Group By RacingData.CustID, Latestlap.autono, customers.racername,customers.FName,customers.LName,LatestLap.ltime, customers.rpm, Latestlap.ambtime

) racing



ORDER BY Case When @Winby = 1 Then racing.lap_num END DESC, 

		 Case When @Winby = 1 Then racing.ambtime END ASC, 

		 Case When @Winby = 0 Then racing.fastest_lap_time END ASC	

End
EOD;

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// Execute statement
$result = $conn->query($dropProcedure);

$result = $conn->query($createProcedure);
print_r($result);

// Confirm success
die('Successfully updated Scoreboard stored procedure');