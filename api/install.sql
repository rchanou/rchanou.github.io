USE [Clubspeedv8]

Go----

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetAccoutingReport]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[GetAccoutingReport]

Go----

CREATE PROCEDURE [dbo].[GetAccoutingReport]
@StDate datetime,
@EndDate datetime
AS 

--Payments
SELECT     PayType.TypeDescription + ' Payment'  as Description, SUM(PayAmount) AS Debit, 0.00 as Credit
FROM         Payment LEFT OUTER JOIN PayType ON Payment.PayType = PayType.ID
WHERE     (PayStatus = 1) AND Payment.PayType<>2 AND Payment.PayType<>3 AND (PayDate BETWEEN @StDate AND @EndDate)
GROUP BY PayType.TypeDescription
 
UNION ALL

--Credit Card Payments

SELECT     ISNULL(Payment.CardType,'NA') + ' Credit Card Payment' as Description , SUM(Payment.PayAmount) AS Debit, 0.00 as Credit
FROM         Payment 
WHERE   (Payment.PayType = 2) AND   (Payment.PayStatus = 1) AND (Payment.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY  ISNULL(Payment.CardType,'NA')

UNION ALL

--External Payments
SELECT     ISNULL(Payment.ExtCardType,'NA') + ' External Payment' as Description , SUM(Payment.PayAmount) AS Debit, 0.00 as Credit
FROM         Payment 
WHERE   (Payment.PayType = 3) AND   (Payment.PayStatus = 1) AND (Payment.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY  ISNULL(Payment.ExtCardType,'NA')

UNION ALL

--SalesByClass-SalesByClassDiscount 

SELECT ProductClasses.Description + ' Sales', 0.00 as Debit,  ISNULL(C.Amount,0) as Credit
FROM ProductClasses 
LEFT OUTER JOIN (
	  SELECT -1 * SUM(ROUND((CheckDetails.UnitPrice * CheckDetails.Qty),2) ) AS Amount, 
	  Products.ProductClassID
	  FROM Checks INNER JOIN CheckDetails ON Checks.CheckID = CheckDetails.CheckID 
	  INNER JOIN Products ON CheckDetails.ProductID = Products.ProductID 
	  WHERE (Checks.CheckStatus = 1) AND (CheckDetails.Status <> 2) AND (Checks.ClosedDate BETWEEN @StDate AND @EndDate) 
	  AND (CheckDetails.ProductID NOT IN (SELECT ProductID FROM Products WHERE ProductType = 7))
	  GROUP BY Products.ProductClassID
	  ) C 
ON ProductClasses.ProductClassID = C.ProductClassID
WHERE ISNULL(C.Amount,0)<>0

UNION ALL

SELECT 'Item Discount', SUM(ROUND(CheckDetails.DiscountApplied,2)) AS Debit, 0.00 as Credit
FROM Checks INNER JOIN CheckDetails ON Checks.CheckID = CheckDetails.CheckID 
INNER JOIN Products ON CheckDetails.ProductID = Products.ProductID 
WHERE (Checks.CheckStatus = 1) AND (CheckDetails.Status <> 2) AND (Checks.ClosedDate BETWEEN @StDate AND @EndDate) 
AND (CheckDetails.ProductID NOT IN (SELECT ProductID FROM Products WHERE ProductType = 7))

UNION ALL

--Taxes           

SELECT 'Taxes(Taxable Payment)',0 as Debit,ISNULL(SUM(Round(Round((UnitPrice * Qty) - DiscountApplied,2) * TaxPercent / 100.0,2)),0.00) *-1 as Credit
FROM      Checks INNER JOIN   dbo.CheckDetails ON Checks.CheckID =  dbo.CheckDetails.CheckID
WHERE     (dbo.CheckDetails.Status <> 2) AND    (dbo.Checks.ClosedDate BETWEEN @StDate  AND @EndDate )

UNION ALL

--PrePayments     

SELECT 'Pre Payments', 0.00 as Debit, ISNULL(SUM(dbo.Payment.PayAmount), 0) * -1 as Credit
FROM         dbo.Checks INNER JOIN
			 dbo.Payment ON dbo.Checks.CheckID = dbo.Payment.CheckID
-- prepayments include the open Checks that paid today plus
			--close Checks that paid after today
WHERE     ((dbo.Checks.CheckStatus = 0) AND (dbo.Payment.PayStatus = 1) AND (dbo.Payment.PayDate BETWEEN @StDate AND  @EndDate)) OR 
			 ((dbo.Checks.CheckStatus = 1) AND (dbo.Payment.PayStatus = 1) AND (dbo.Payment.PayDate BETWEEN @StDate AND  @EndDate ) AND (dbo.Checks.ClosedDate >  @EndDate ) )     

UNION ALL

--PrePaymentsUsed 

SELECT 'Pre Payments Used', ISNULL(SUM(dbo.Payment.PayAmount),0) as Debit, 0.00 as Credit
FROM         dbo.Checks INNER JOIN
					  dbo.Payment ON dbo.Checks.CheckID = dbo.Payment.CheckID
WHERE     (dbo.Checks.CheckStatus = 1) AND (dbo.Payment.PayStatus = 1)
 AND (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) AND 
					 (PayDate <  @StDate )

UNION ALL

--Expenses        
SELECT  'Expenses', ISNULL(SUM(Amount),0) as Debit, 0.00 as Credit
FROM         dbo.Expenses
WHERE [DATE] BETWEEN @StDate  AND  @EndDate

UNION ALL

--OverPaid        

SELECT 'Over Paid', 0.00 as Debit, ISNULL(SUM(Overpaid)  ,0)*-1 as Credit
FROM         OverPaid 
WHERE  CheckID IN (SELECT CheckID FROM Checks WHERE    (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) AND (dbo.Checks.CheckStatus = 1))

UNION ALL

--GiftCard        

SELECT  'Gift Card', 0.00 as Debit, ISNULL(Round(SUM((dbo.CheckDetails.UnitPrice * dbo.CheckDetails.Qty) - dbo.CheckDetails.DiscountApplied),2), 0) * -1 as Credit
FROM  dbo.Checks INNER JOIN
	  dbo.CheckDetails ON dbo.Checks.CheckID = dbo.CheckDetails.CheckID
WHERE     (dbo.Checks.CheckStatus = 1) AND (CheckDetails.Status <> 2) AND 
	  (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) 
						AND (dbo.CheckDetails.ProductID IN
						  (SELECT     ProductID
							FROM          dbo.Products
							WHERE   ProductType = 7
				  ))
 
UNION ALL

--Gratuity  

SELECT  'Gratuity', 0.00 as Debit, ISNULL(SUM(Gratuity),0)*-1 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)      
 
UNION ALL

--Fee 
SELECT  'Fee', 0.00 as Debit, ISNULL(SUM(Fee),0)*-1 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)      
     
UNION ALL

--CheckDiscount   

SELECT  'Check Discount', ISNULL(SUM(Discount),0) as Debit, 0.00 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)


Go----

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetScoreboard]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[GetScoreboard]

Go----

set ANSI_NULLS ON
set QUOTED_IDENTIFIER ON
GO

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
				 Else --Case above was to check if winby best time 
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

Go----

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetNextHeatInfo]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[GetNextHeatInfo]

Go----

CREATE PROCEDURE [dbo].[GetNextHeatInfo]
@TrackNo int
AS
BEGIN
	Select top 1 hm.Heatno id, hm.TrackNo track_id, t.Description track, hm.ScheduledTime starts_at, hm.HeatTypeNo heat_type_id, 
		   hm.HeatStatus heat_status_id, hm.SpeedLevel speed_level_id, sl.Description speed_level,
		   case when hm.WinBy = 0 then 'laptime' else 'position' end win_by,
		   Case when hm.RaceBy = 0 then 'minutes' else 'laps' end race_by, 
		   hm.ScheduleDuration duration, ht.heattypename race_name From HeatMain hm
	inner join HeatTypes ht on hm.HeatTypeNo = ht.HeatTypeNo
	inner join SpeedLevel sl on hm.SpeedLevel = sl.SpeedLevel
	inner join Tracks t on hm.trackno = t.TrackNo
	Where hm.ScheduledTime >= GetDate() and hm.HeatStatus = 0 and hm.Begining is null and hm.Finish is null and hm.TrackNo = @TrackNo
	order by hm.scheduledtime
END

Go----

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetNextHeatRacersInfo]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[GetNextHeatRacersInfo]

Go----

CREATE PROCEDURE [dbo].[GetNextHeatRacersInfo]
@HeatNo int
AS
BEGIN
	Select hd.CustID id, hd.LineUpPosition start_position, hd.RPM rpm,
		   case when c.TotalRaces > 1 then 1 else 0 end is_first_time,
		   hd.FinishPosition finish_position, c.RacerName nickname, c.FName first_name, c.LName last_name From HeatMain hm
	inner join HeatTypes ht on hm.HeatTypeNo = ht.HeatTypeNo
	inner join SpeedLevel sl on hm.SpeedLevel = sl.SpeedLevel
	inner join Tracks t on hm.trackno = t.TrackNo
	inner join HeatDetails hd on hm.heatno = hd.heatno
	inner join Customers c on hd.CustID = c.CustID
	Where hd.HeatNo = @HeatNo
	order by hd.LineUpPosition
END

Go----

Go----

IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetHeatInfoByHeatNo]') AND type in (N'P', N'PC'))
  DROP PROCEDURE [dbo].[GetHeatInfoByHeatNo]

Go----

Go----

CREATE PROCEDURE [dbo].[GetHeatInfoByHeatNo]
    @HeatNo int
AS
  BEGIN
    Select top 1 hm.Heatno id, hm.TrackNo track_id, t.Description track, hm.ScheduledTime starts_at, hm.HeatTypeNo heat_type_id,
                 hm.HeatStatus heat_status_id, hm.SpeedLevel speed_level_id, sl.Description speed_level,
                 case when hm.WinBy = 0 then 'laptime' else 'position' end win_by,
                 Case when hm.RaceBy = 0 then 'minutes' else 'laps' end race_by,
                 hm.ScheduleDuration duration, ht.heattypename race_name From HeatMain hm
      inner join HeatTypes ht on hm.HeatTypeNo = ht.HeatTypeNo
      inner join SpeedLevel sl on hm.SpeedLevel = sl.SpeedLevel
      inner join Tracks t on hm.trackno = t.TrackNo
    Where hm.Heatno = @HeatNo
    order by hm.scheduledtime
  END

Go----