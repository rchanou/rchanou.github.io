<?php
/**
 * This script creates/updates the GetAccountExport, a query that gives a
 * General Ledger style entry used in double entry accounting.
 */

$dropProcedure = <<<EOD
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GetAccountingExport]') AND type in (N'P', N'PC'))

DROP PROCEDURE [dbo].[GetAccountingExport]
EOD;

$createProcedure = <<<EOD
CREATE PROCEDURE [dbo].[GetAccountingExport]
@StDate datetime,
@EndDate datetime
AS 

--Payments
SELECT    REPLACE(UPPER('##' + PayType.TypeDescription + ' Payment##'), ' ', '_') AS AccountNumber, PayType.TypeDescription + ' Payment'  as Description, SUM(PayAmount) AS Debit, 0.00 as Credit
FROM         Payment LEFT OUTER JOIN PayType ON Payment.PayType = PayType.ID
WHERE     (PayStatus = 1) AND Payment.PayType<>2 AND Payment.PayType<>3 AND (PayDate BETWEEN @StDate AND @EndDate)
GROUP BY PayType.TypeDescription
 
UNION ALL

--Credit Card Payments

SELECT    REPLACE(UPPER('##' + ISNULL(Payment.CardType,'NA') + ' Credit Card Payment##'), ' ', '_') AS AccountNumber, ISNULL(Payment.CardType,'NA') + ' Credit Card Payment' as Description , SUM(Payment.PayAmount) AS Debit, 0.00 as Credit
FROM         Payment 
WHERE   (Payment.PayType = 2) AND   (Payment.PayStatus = 1) AND (Payment.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY  ISNULL(Payment.CardType,'NA')

UNION ALL

--External Payments
SELECT   REPLACE(UPPER('##' + ISNULL(Payment.ExtCardType,'NA') + ' External Payment##'), ' ', '_') AS AccountNumber, ISNULL(Payment.ExtCardType,'NA') + ' External Payment' as Description , SUM(Payment.PayAmount) AS Debit, 0.00 as Credit
FROM         Payment 
WHERE   (Payment.PayType = 3) AND   (Payment.PayStatus = 1) AND (Payment.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY  ISNULL(Payment.ExtCardType,'NA')

UNION ALL

--SalesByClass-SalesByClassDiscount 

SELECT ProductClasses.ExportName AS AccountNumber, ProductClasses.Description + ' Sales', 0.00 as Debit,  ISNULL(C.Amount,0) as Credit
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

SELECT '##ITEM_DISCOUNT##' AS AccountNumber, 'Item Discount', SUM(ROUND(CheckDetails.DiscountApplied,2)) AS Debit, 0.00 as Credit
FROM Checks INNER JOIN CheckDetails ON Checks.CheckID = CheckDetails.CheckID 
INNER JOIN Products ON CheckDetails.ProductID = Products.ProductID 
WHERE (Checks.CheckStatus = 1) AND (CheckDetails.Status <> 2) AND (Checks.ClosedDate BETWEEN @StDate AND @EndDate) 
AND (CheckDetails.ProductID NOT IN (SELECT ProductID FROM Products WHERE ProductType = 7))

UNION ALL

--Taxes           

SELECT '##TAXES##' AS AccountNumber, 'Taxes (Taxable Payment)',0 as Debit,ISNULL(SUM(Round(Round((UnitPrice * Qty) - DiscountApplied,2) * TaxPercent / 100.0,2)),0.00) *-1 as Credit
FROM      Checks INNER JOIN   dbo.CheckDetails ON Checks.CheckID =  dbo.CheckDetails.CheckID
WHERE     (dbo.CheckDetails.Status <> 2) AND    (dbo.Checks.ClosedDate BETWEEN @StDate  AND @EndDate )

UNION ALL

--PrePayments     

SELECT '##PREPAYMENTS##' AS AccountNumber, 'Pre Payments', 0.00 as Debit, ISNULL(SUM(dbo.Payment.PayAmount), 0) * -1 as Credit
FROM         dbo.Checks INNER JOIN
			 dbo.Payment ON dbo.Checks.CheckID = dbo.Payment.CheckID
-- prepayments include the open Checks that paid today plus
			--close Checks that paid after today
WHERE     ((dbo.Checks.CheckStatus = 0) AND (dbo.Payment.PayStatus = 1) AND (dbo.Payment.PayDate BETWEEN @StDate AND  @EndDate)) OR 
			 ((dbo.Checks.CheckStatus = 1) AND (dbo.Payment.PayStatus = 1) AND (dbo.Payment.PayDate BETWEEN @StDate AND  @EndDate ) AND (dbo.Checks.ClosedDate >  @EndDate ) )     

UNION ALL

--PrePaymentsUsed 

SELECT '##PREPAYMENTS_USED##' AS AccountNumber, 'Pre Payments Used', ISNULL(SUM(dbo.Payment.PayAmount),0) as Debit, 0.00 as Credit
FROM         dbo.Checks INNER JOIN
					  dbo.Payment ON dbo.Checks.CheckID = dbo.Payment.CheckID
WHERE     (dbo.Checks.CheckStatus = 1) AND (dbo.Payment.PayStatus = 1)
 AND (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) AND 
					 (PayDate <  @StDate )

UNION ALL

--Expenses        
SELECT  '##EXPENSES##' AS AccountNumber, 'Expenses', ISNULL(SUM(Amount),0) as Debit, 0.00 as Credit
FROM         dbo.Expenses
WHERE [DATE] BETWEEN @StDate  AND  @EndDate

UNION ALL

--OverPaid        

--SELECT 'Over Paid', 0.00 as Debit, ISNULL(SUM(Overpaid)  ,0)*-1 as Credit
--FROM         OverPaid 
--WHERE  CheckID IN (SELECT CheckID FROM Checks WHERE    (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) AND (dbo.Checks.CheckStatus = 1))

--UNION ALL

--GiftCard        

SELECT  '##GIFTCARDS##' AS AccountNumber, 'Gift Card', 0.00 as Debit, ISNULL(Round(SUM((dbo.CheckDetails.UnitPrice * dbo.CheckDetails.Qty) - dbo.CheckDetails.DiscountApplied),2), 0) * -1 as Credit
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

SELECT  '##GRATUITY##' AS AccountNumber, 'Gratuity', 0.00 as Debit, ISNULL(SUM(Gratuity),0)*-1 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)      
 
UNION ALL

--Fee 
SELECT  '##FEE##' AS AccountNumber, 'Fee', 0.00 as Debit, ISNULL(SUM(Fee),0)*-1 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)      
     
UNION ALL

--CheckDiscount   

SELECT  '##CHECK_DISCOUNT##' AS AccountNumber, 'Check Discount', ISNULL(SUM(Discount),0) as Debit, 0.00 as Credit
FROM      Checks 
WHERE       (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate)
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
die('Successfully updated GetAccountingExport stored procedure');