<?php
/**
 * This script creates/updates the GetAccountExport, a query that gives a
 * General Ledger style entry used in double entry accounting.
 */

require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

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
SELECT
      REPLACE(UPPER('##' + pt.TypeDescription + ' Payment##'), ' ', '_') AS AccountNumber
    , pt.TypeDescription + ' Payment'  AS Description
    , SUM(p.PayAmount) AS Debit
    , 0.00 AS Credit
FROM Payment p
LEFT OUTER JOIN PayType pt
    ON p.PayType = pt.ID
WHERE
    (p.PayStatus = 1)
    AND p.PayType <> 2
    AND p.PayType <> 3
    AND (p.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY pt.TypeDescription
 
UNION ALL

--Credit Card Payments
SELECT
      REPLACE(UPPER('##' + ISNULL(p.CardType,'NA') + ' Credit Card Payment##'), ' ', '_') AS AccountNumber
    , ISNULL(p.CardType,'NA') + ' Credit Card Payment' AS Description 
    , SUM(p.PayAmount) AS Debit
    , 0.00 AS Credit
FROM Payment  p
WHERE
    (p.PayType = 2)
    AND (p.PayStatus = 1)
    AND (p.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY
    ISNULL(p.CardType,'NA')

UNION ALL

--External Payments
SELECT
      REPLACE(UPPER('##' + ISNULL(p.ExtCardType,'NA') + ' External Payment##'), ' ', '_') AS AccountNumber
    , ISNULL(p.ExtCardType,'NA') + ' External Payment' AS Description
    , SUM(p.PayAmount) AS Debit
    , 0.00 AS Credit
FROM Payment p
WHERE
    (p.PayType = 3)
    AND (p.PayStatus = 1)
    AND (p.PayDate BETWEEN @StDate AND @EndDate)
GROUP BY 
    ISNULL(p.ExtCardType,'NA')

UNION ALL

--SalesByClass-SalesByClassDiscount 

SELECT
      pc.ExportName AS AccountNumber
    , pc.Description + ' Sales'
    , 0.00 AS Debit
    , ISNULL(c.Amount, 0) + CASE WHEN dbo.UseSalesTax() = 0 THEN c.CheckDetailTax ELSE 0 END AS Credit
FROM ProductClasses pc
LEFT OUTER JOIN (
    SELECT 
        -1 * SUM( ROUND( ( ctv.UnitPrice * ctv.Qty ),2 ) ) AS Amount,
        SUM(ctv.CheckDetailTax) AS CheckDetailTax,
        p.ProductClassID
    FROM CheckTotals_V ctv
    INNER JOIN Products p
        ON ctv.ProductID = p.ProductID 
    WHERE
        (ctv.CheckStatus = 1)
        AND (ctv.CheckDetailStatus <> 2)
        AND (ctv.ClosedDate BETWEEN @StDate AND @EndDate) 
        AND (ctv.ProductID NOT IN (SELECT ProductID FROM Products WHERE ProductType = 7))
    GROUP BY
        p.ProductClassID
) c
    ON pc.ProductClassID = c.ProductClassID
WHERE
    ISNULL(c.Amount,0) <> 0


UNION ALL

SELECT
      '##ITEM_DISCOUNT##' AS AccountNumber
    , 'Item Discount'
    , ISNULL( SUM( ROUND( cd.DiscountApplied, 2 ) ), 0 ) AS Debit
    , 0.00 AS Credit
FROM Checks c
INNER JOIN CheckDetails cd
    ON c.CheckID = cd.CheckID 
INNER JOIN Products p
    ON cd.ProductID = p.ProductID 
WHERE
    (c.CheckStatus = 1)
    AND (cd.Status <> 2)
    AND (c.ClosedDate BETWEEN @StDate AND @EndDate) 
    AND (cd.ProductID NOT IN (SELECT ProductID FROM Products WHERE ProductType = 7))

UNION ALL

--Taxes           

SELECT
      '##TAXES##' AS AccountNumber
    , 'Taxes(Taxable Payment)'
    , 0 AS Debit
    , ISNULL(SUM(ctv.CheckDetailTax)* -1 , 0) AS Credit
FROM CheckTotals_V ctv
WHERE 
        (ctv.CheckDetailStatus <> 2)
    AND (ctv.ClosedDate BETWEEN @StDate AND @EndDate)

UNION ALL

--PrePayments     

SELECT
      '##PREPAYMENTS##' AS AccountNumber
    , 'Pre Payments'
    , 0.00 AS Debit
    , ISNULL(SUM(p.PayAmount), 0) * -1 AS Credit
FROM dbo.Checks_V c
INNER JOIN dbo.Payment p
    ON c.CheckID = p.CheckID
WHERE (
    (
        (c.CheckStatus = 0)
        AND (p.PayStatus = 1)
        AND (p.PayDate BETWEEN @StDate AND  @EndDate)
    )
    OR 
    (
        (c.CheckStatus = 1)
        AND (p.PayStatus = 1)
        AND (p.PayDate BETWEEN @StDate AND @EndDate )
        AND (c.ClosedDate >  @EndDate )
    )
)

UNION ALL

--PrePaymentsUsed 

SELECT
      '##PREPAYMENTS_USED##' AS AccountNumber
    , 'Pre Payments Used'
    , ISNULL(SUM(p.PayAmount), 0) AS Debit
    , 0.00 AS Credit
FROM dbo.Checks_V c
INNER JOIN dbo.Payment p
    ON c.CheckID = p.CheckID
WHERE
    (c.CheckStatus = 1)
    AND (p.PayStatus = 1)
    AND (c.ClosedDate BETWEEN @StDate AND @EndDate)
    AND (p.PayDate < @StDate)

UNION ALL

--Expenses        
SELECT
      '##EXPENSES##' AS AccountNumber
    , 'Expenses'
    , ISNULL(SUM(e.Amount), 0) AS Debit
    , ISNULL(SUM(e.Amount), 0) * -1 AS Credit
FROM dbo.Expenses e
WHERE
    e.[Date] BETWEEN @StDate AND @EndDate

UNION ALL

--OverPaid        

--SELECT 'Over Paid', 0.00 as Debit, ISNULL(SUM(Overpaid)  ,0)*-1 as Credit
--FROM         OverPaid 
--WHERE  CheckID IN (SELECT CheckID FROM Checks WHERE    (dbo.Checks.ClosedDate BETWEEN @StDate  AND  @EndDate) AND (dbo.Checks.CheckStatus = 1))

--UNION ALL

--GiftCard        

SELECT
      '##GIFTCARDS##' AS AccountNumber
    , 'Gift Card'
    , 0.00 AS Debit
    , ISNULL(SUM(cd.CheckDetailSubtotal * -1), 0) AS Credit
FROM dbo.Checks_V c
INNER JOIN dbo.CheckDetails_V cd
    ON c.CheckID = cd.CheckID
WHERE
    c.CheckStatus = 1
    AND cd.CheckDetailStatus != 2
    AND c.ClosedDate BETWEEN @StDate AND @EndDate
    AND cd.ProductID IN (
        SELECT ProductID
        FROM dbo.Products
        WHERE ProductType = 7
    )
 
UNION ALL

--Gratuity  

SELECT
      '##GRATUITY##' AS AccountNumber
    , 'Gratuity'
    , 0.00 AS Debit
    , ISNULL(SUM(c.Gratuity),0 ) * -1 AS Credit
FROM dbo.Checks c 
WHERE
    (c.ClosedDate BETWEEN @StDate AND @EndDate)
 
UNION ALL

--Fee 
SELECT
      '##FEE##' AS AccountNumber
    , 'Fee', 0.00 AS Debit
    , ISNULL(SUM(c.Fee), 0) * -1 AS Credit
FROM dbo.Checks c 
WHERE
    (c.ClosedDate BETWEEN @StDate AND @EndDate)
     
UNION ALL

--CheckDiscount   

SELECT
      '##CHECK_DISCOUNT##' AS AccountNumber
    , 'Check Discount'
    , ISNULL(SUM(c.Discount), 0) AS Debit
    , 0.00 AS Credit
FROM dbo.Checks c 
WHERE
    (c.ClosedDate BETWEEN @StDate AND @EndDate)
EOD;

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// Execute statement
$result = $conn->query($dropProcedure);

$result = $conn->query($createProcedure);

/**
 * CREATE MAPPING SETTING
 */
$settings = array(
  array(
    'Namespace'    => 'AccountingExport',
    'Name'         => 'fieldMappings',
    'Type'         => 'string',
    'DefaultValue' => "",
    'Value'        => "",
    'Description'  => 'Mapping of ##PLACEHOLDERS## to Account Numbers and other related variables',
    'IsPublic'     => true
  ),
);


foreach($settings as $setting) {
    try {
        $existing = $db->settings->match(array(
            'Namespace' => $setting['Namespace'],
						'Name'      => $setting['Name']
        ));
        if (empty($existing)) {
            $db->settings->create($setting);
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name']  . ') successfully imported!';
            echo '<br>';
        }
        else {
            echo 'Setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ') already exists!';
            echo '<br>';
        }
    }
    catch (Exception $e) {
        echo 'Unable to import setting (' . $setting['Namespace'] . ', ' . $setting['Name'] . ')! ' . $e->getMessage();
        echo '<br>';
    }
}

// Confirm success
die('Successfully created/updated GetAccountingExport stored procedure and fieldMappings setting');