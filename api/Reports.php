<?php

use ClubSpeed\Utility\Strings as Strings;
use ClubSpeed\Connection as Connection;
use ClubSpeed\Utility\Convert as Convert;

class Reports extends BaseApi
{
    public $restler;
    
    function __construct() {
        // header('Access-Control-Allow-Origin: *'); //Here for all /say
        parent::__construct();
    }
    
    /*
    // Period Totals

SELECT     SUM(CheckTotal) AS Total, COUNT(*) AS TotalSales
FROM         Checks
WHERE     (ClosedDate BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59')

// Sales By Hour

SELECT     MAX(DATEPART(hh, ClosedDate)) AS Hour, SUM(CheckTotal) AS Total, COUNT(*) AS TotalSales
FROM         Checks
WHERE     (ClosedDate BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59')
GROUP BY DATEPART(hh, ClosedDate)

/// Total Racers

SELECT     COUNT(HeatDetails.FirstTime) AS TotalRacers
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59')

/// Total Racers by Hour

SELECT     MAX(DATEPART(hh, HeatMain.ScheduledTime)) AS Hour, COUNT(HeatDetails.FirstTime) AS TotalRacers
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59')
GROUP BY DATEPART(hh, HeatMain.ScheduledTime)

/// First Timers

SELECT     COUNT(HeatDetails.FirstTime) AS FirstTimeRacers
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59') AND (HeatDetails.FirstTime = 'true')

/// Period's Best Sellers

SELECT     MAX(CheckDetails.ProductName) AS ProductName, COUNT(*) AS Total
FROM         Checks LEFT OUTER JOIN
                      CheckDetails ON Checks.CheckID = CheckDetails.CheckID
WHERE     (Checks.ClosedDate BETWEEN '2012-12-17 00:00:00' AND '2012-12-17 23:59:59')
GROUP BY CheckDetails.ProductID
ORDER BY Total DESC
*/

    private function getDateRange() {
        if (isset($_REQUEST['start']))
            $start = Convert::toDateForServer($_REQUEST['start']);
        else {
            $start = new DateTime();
            $start->setTime(0, 0, 0); // remove time from date
            $start = Convert::toDateForServer($start); // get it back as a string
        }
        if (isset($_REQUEST['end']))
            $end = Convert::toDateForServer($_REQUEST['end']);
        else {
            $end = new DateTime($start); // get a copy of the start date
            $end->setTime(23, 59, 59); // set time to the end of the day
            $end = Convert::toDateForServer($end); // get it back as a string
        }
        return array(
            'start' => $start
            , 'end' => $end
        );
    }

		/**
     * @url GET /marketing_source_performance
     */
    public function marketing_source_performance() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");

        $opened_or_closed_date = isset($_REQUEST['show_by_opened_date']) && $_REQUEST['show_by_opened_date'] === 'true' ? 'c.OpenedDate' : 'c.ClosedDate';

        $sql = <<<EOS
SELECT cust.sourceid AS SourceID, s.SourceName, SUM(c.CheckTotal) AS TotalSpent FROM Sources s
LEFT JOIN Customers cust ON cust.SourceID = s.SourceID
LEFT JOIN checks c on cust.CustID = c.custid
WHERE c.CustID > 0
AND {$opened_or_closed_date} BETWEEN :start AND :end
GROUP BY cust.sourceid, s.SourceName
ORDER BY TotalSpent DESC
EOS;

        $params = array(&$start, &$end);
				$data = $this->run_query($sql, $this->getDateRange());
        return $data;
    }
		
		/**
     * @url GET /social
		 * Get the customers using Facebook (could be expanded in the future
		 * to other social media platforms).
     */
    public function social() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");

        $sql = <<<EOS
SELECT fb.UId AS facebookUserId, c.*
  FROM dbo.FB_Customers_New fb 
  LEFT JOIN Customers c ON
  fb.CustId = c.custID
  WHERE fb.Enabled = 1
EOS;

				$data = $this->run_query($sql);
        return $data;
    }

	/**
	 * DETAILED PAYMENTS REPORT
	 *
	 * Show the detailed breakdown of the payments taken in a date range for each check
	 */
	public function payments() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }	
		$tsql = <<<EOD
select

case
when c.checkstatus = 0 then 'Open' else 'Closed' end AS 'Check Status',
c.OpenedDate AS 'Opened On',
c.ClosedDate AS 'Closed On',
c.CheckID AS 'Check ID',
c.CustID as 'Customer ID',
cust.LName AS 'Customer Last Name',
cust.FName AS 'Customer First Name',
u.UserName AS 'Created By',
c.checktotal AS 'Check Total',
p.PayAmount AS 'Pay Amount',
p.Shift AS 'Shift',
u2.username AS 'Cashed By', 

case 
when p.paytype = 1 then 'Cash' 
when p.PayType = 2 then 'Credit Card' 
when p.PayType = 3 then 'External Payment' 
when p.PayType = 4 then 'Gift Card' 
when p.PayType = 5 then 'Voucher' 
when p.PayType = 6 then 'Complimentary' 
when p.PayType = 7 then 'Check' 
when p.PayType = 8 then 'Game Card' 
when p.PayType = 9 then 'Debit Card' 
end as Tender,

p.PayTerminal AS 'Pay Terminal', p.PayDate AS 'Paid On'

from Checks c
left join Customers cust on c.CustID = cust.CustID
left join Payment p on p.CheckID = c.checkid
left join Users u on u.UserID = c.userid
left join Users u2 on u2.UserID = p.userid
where p.paydate between :start and :end
and p.paystatus = 1
order by p.paydate
EOD;
        $dates = $this->getDateRange();
        $data = $this->run_query($tsql, $dates);
		return $data;
	}

    /**
     * @url GET /payments/eurekas
     */
    public function payments_eurekas() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        if (!$this->logic->version->hasEurekas())
            throw new RestException(404, "Eurekas database could not be found!");
        $sql = <<<EOS
SELECT
    CASE WHEN c.CheckStatus = 0 THEN 'Open' ELSE 'Closed' END AS 'Check Status'
    , c.OpenedDate AS 'Opened On'
    , c.ClosedDate AS 'Closed On'
    , c.CheckID AS 'Check ID'
    , NULL AS 'Customer ID'
    , NULL AS 'Customer Last Name'
    , NULL AS 'Customer First Name'
    , u.UserName AS 'Created By'
    , c.CheckTotal AS 'Check Total'
    , p.PaymentAmount AS 'Pay Amount'
    , p.Shift AS 'Shift'
    , u2.UserName AS 'Cashed By'
    , pt.PaymentType AS 'Tender'
    , p.TerminalName AS 'Pay Terminal'
    , p.TransactionDate AS 'Paid On'
FROM dbo.Checks c
LEFT OUTER JOIN dbo.Payment p
    ON c.CheckID = p.CheckID
LEFT OUTER JOIN dbo.PaymentType pt
	ON pt.ID = p.PaymentType
LEFT OUTER JOIN dbo.Users u
    ON c.UserID = u.UserID
LEFT OUTER JOIN dbo.Users u2
    ON p.UserID = u2.UserID
WHERE
    p.TransactionDate BETWEEN :start AND :end
    AND p.IsVoid = 0
ORDER BY
    p.TransactionDate
EOS;
        $conn = new Connection\ClubSpeedConnection("(local)", "RestaurantPiece");
        $data = $conn->query($sql, $this->getDateRange());
        return $data;
    }


	/**
	 * SUMMARY PAYMENTS REPORT
	 *
	 * Show the summary of the payments in a date range by tender
	 */
	public function payments_summary() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        $tsql = <<<EOS
SELECT
    pt.TypeDescription AS 'Tender'
    , SUM(p.PayAmount) AS 'Total Amount'
FROM
    dbo.Payment p
INNER JOIN dbo.PayType pt
    ON p.PayType = pt.ID
WHERE
    p.PayStatus = 1
    AND p.PayDate BETWEEN :start AND :end
GROUP BY
    pt.TypeDescription
EOS;
        $data = $this->run_query($tsql, $this->getDateRange());
		return $data;
	}

    /**
     * @url GET /payments_summary/eurekas
     */
    public function payments_summary_eurekas() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        if (!$this->logic->version->hasEurekas())
            throw new RestException(404, "Eurekas database could not be found!");
        $sql = <<<EOS
SELECT
    pt.PaymentType AS 'Tender'
    , SUM(p.PaymentAmount) AS 'Total Amount'
FROM
    dbo.Payment p
INNER JOIN dbo.PaymentType pt
    ON p.PaymentType = pt.ID
WHERE
    p.IsVoid = 0
    AND p.TransactionDate BETWEEN :start AND :end
GROUP BY
    pt.PaymentType
EOS;
        $conn = new Connection\ClubSpeedConnection("(local)", "RestaurantPiece");
        $data = $conn->query($sql, $this->getDateRange());
        return $data;
    }

    /**
     * @url GET /accounting
     */
    public function accouting() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");

        $sql = <<<EOS
EXEC GetAccountingExport :start, :end
EOS;
        $params = array(&$start, &$end);
				$data = $this->run_query($sql, $this->getDateRange());
        return $data;
    }
		
		/**
     * @url GET /brokers_summary
     */
    public function brokers_summary() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");

        $opened_or_closed_date = isset($_REQUEST['show_by_opened_date']) && $_REQUEST['show_by_opened_date'] === 'true' ? 'c.OpenedDate' : 'c.ClosedDate';

        $sql = <<<EOS
SELECT
    c.BrokerName AS 'Broker/Affiliate Code'
    , SUM(p.PayAmount) AS 'Total Amount'
FROM dbo.Checks c
INNER JOIN dbo.Payment p
    ON p.CheckID = c.CheckID
WHERE
    p.PayStatus = 1
    AND c.BrokerName IS NOT NULL
    AND LEN(c.BrokerName) > 0
		AND {$opened_or_closed_date} BETWEEN :start AND :end
GROUP BY
    c.BrokerName
ORDER BY c.BrokerName
EOS;
        $params = array(&$start, &$end);
				$data = $this->run_query($sql, $this->getDateRange());
        return $data;
    }


	/**
	 * DETAILED SALES REPORT
	 *
	 * Shows the line items of every check in a date range.
	 * 
	 * Depending on the country, some will want to see items by either the check *closing* or *opening* date. In UK and
	 * most of Europe, taxes are owed when the service is provided (open date), not when the check is paid (closed date).
	 *
	 * We default to US-style, showing by check *closed* date. The "show_by_open_date=true" flag will show checks
	 * by their opening date instead.
	 */
	public function sales() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
		
		$opened_or_closed_date = isset($_REQUEST['show_by_opened_date']) && $_REQUEST['show_by_opened_date'] === 'true' ? 'c.OpenedDate' : 'c.ClosedDate';
		$tsql = <<<EOD
select

c.CheckID AS 'Check ID',
cd.ProductID AS "Product ID",
cd.ProductName AS "Product Name",
cd.UnitPrice AS "Unit Price",
cd.Qty AS "Quantity",
cd.DiscountApplied AS "Total Discount",
cd.TaxPercent AS "Tax Percent",
cd.TaxID AS "Tax ID",
CASE
WHEN cd.Status = 2 THEN 'Voided'
ELSE ''
END
AS "Void Status",
cd.CreatedDate AS "Line Item Created At",
case 
   when exists (
      SELECT 1 
      FROM Sys.columns c 
      WHERE c.[object_id] = OBJECT_ID('dbo.CheckDetails') 
         AND c.name = 'CreatedOn'
   ) 
   then cd.CreatedOn
   else null
END AS 'Line Item Created On',
case 
   when exists (
      SELECT 1 
      FROM Sys.columns c 
      WHERE c.[object_id] = OBJECT_ID('dbo.CheckDetails') 
         AND c.name = 'CreatedBy'
   ) 
   then cd.CreatedBy
   else null
END AS 'Line Item Created By',
products.ProductClassID AS "Product Class ID",
pc.Description AS "Product Class Description",
pc.ExportName AS "Product Class Export",
case
when c.checkstatus = 0 then 'Open' else 'Closed' end AS 'Check Status',
c.OpenedDate AS 'Check Opened On',
c.ClosedDate AS 'Check Closed On',
c.CustID as 'Customer ID',
cust.LName AS 'Customer Last Name',
cust.FName AS 'Customer First Name',
c.BrokerName AS 'Broker/Affiliate',
u.UserName AS 'Created By',
c.checktotal AS 'Check Total',
c.BrokerName AS 'Broker/Affiliate Code',
c.DiscountNotes AS 'Discount Notes'

FROM CheckDetails cd
left join Checks c ON c.CheckID = cd.CheckID
left join Customers cust on c.CustID = cust.CustID
left join Users u on u.UserID = c.userid
left join Products ON Products.ProductID = cd.ProductID
left join ProductClasses pc ON pc.ProductClassID = products.ProductClassID
WHERE {$opened_or_closed_date} BETWEEN :start AND :end
ORDER BY {$opened_or_closed_date}
EOD;
		$params = array(&$start, &$end);
        $data = $this->run_query($tsql, $this->getDateRange());

        // // if we want to "union" eurekas data, this pattern can be followed
        // // but there could be check id collisions / confusion
        // $data = array_merge($data, $this->salesEureka()); // no parameters, using $_GET context for all variables
        // if ($opened_or_closed_date === 'c.OpenedDate')
        //     $key = 'Check Opened On';
        // else
        //     $key = 'Check Closed On';
        // usort($data, function($a, $b) use ($key) {
        //     if ($a[$key] == $b[$key])
        //         return 0; // php order of operations stupidity. don't add this to the ternary below, unless we like adding parentheses everywhere
        //     return ($a[$key] < $b[$key] ? -1 : 1);
        // });
        // // end eurekas union portion

		return $data;
	}

	/**
	 * SALES BY POS, PRODUCT CLASS
	 *
	 * Shows the sales grouped by POS and Product Class
	 *
	 */
	public function sales_by_pos_and_class() {
		if (!\ClubSpeed\Security\Authenticate::privateAccess())
				throw new RestException(401, "Invalid authorization!");
		
		$tsql = <<<EOD
SELECT
MAX(cd.CreatedOn) AS 'POS',
MAX(pc.Description) AS 'Category',
SUM(cdv.CheckDetailTax) AS 'Tax',
SUM(cdv.CheckDetailTotal) AS 'Total'
FROM CheckDetails_V cdv
LEFT JOIN CheckDetails cd ON cdv.CheckDetailID = cd.CheckDetailID
LEFT JOIN Products ON Products.ProductID = cd.ProductID
LEFT JOIN ProductClasses pc ON pc.ProductClassID = products.ProductClassID
WHERE cdv.CreatedDate BETWEEN :start AND :end
GROUP BY cd.CreatedOn, pc.ProductClassID
ORDER BY cd.CreatedOn, pc.ProductClassID
EOD;
		$params = array(&$start, &$end);
    $data = $this->run_query($tsql, $this->getDateRange());

		return $data;
	}

	
	/**
	 * EVENT SALES REP REPORT
	 *
	 * Shows the checks by each sales rep.
	 *
	 */
	public function event_rep_sales() {
		if (!\ClubSpeed\Security\Authenticate::privateAccess())
				throw new RestException(401, "Invalid authorization!");
		
		$opened_or_closed_date = isset($_REQUEST['show_by_opened_date']) && $_REQUEST['show_by_opened_date'] === 'true' ? 'c.OpenedDate' : 'c.ClosedDate';
		$tsql = <<<EOD
;WITH CTE AS (
       SELECT
               p.CheckID
               , SUM(p.PayTax) PayTax
       FROM dbo.Payment p
       WHERE p.PayStatus <> 2
       GROUP BY p.CheckID
)
SELECT u.UserName, u.FName AS FirstName, u.LName AS LastName, er.Subject, er.Description, er.StartTime, er.Notes, c.CheckID, c.CustID, c.CheckName, c.Notes as CheckNotes, c.Gratuity, c.Fee, cte.PayTax AS Tax, c.CheckTotal, c.OpenedDate, c.ClosedDate FROM EventReservations er
LEFT JOIN EventReservationLink erl ON er.ID = erl.ReservationID
LEFT JOIN Checks c ON erl.CheckID = c.CheckID
INNER JOIN CTE cte
       ON c.CheckID = cte.CheckID
LEFT JOIN Users u ON u.UserID = er.RepID
WHERE {$opened_or_closed_date} BETWEEN :start AND :end
ORDER BY {$opened_or_closed_date}
EOD;
		$params = array(&$start, &$end);
    $data = $this->run_query($tsql, $this->getDateRange());

		return $data;
	}

    /**
     * @url GET /sales/eurekas
     */
    public function sales_eurekas() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess())
            throw new RestException(401, "Invalid authorization!");
        if (!$this->logic->version->hasEurekas())
            throw new RestException(404, "Eurekas database could not be found!");
        $opened_or_closed_date = isset($_REQUEST['show_by_opened_date']) && $_REQUEST['show_by_opened_date'] === 'true' ? 'c.OpenedDate' : 'c.ClosedDate';
        $sql = <<<EOS
SELECT
    c.CheckID
    , cli.ItemID AS 'Product ID'
    , cli.ItemDescription AS 'Product Name'
    , cli.UnitPrice
    , cli.Qty AS 'Quantity'
    , cli.ItemDiscount AS 'Total Discount' -- cli.TaxDiscount exists. usage
    , cli.TaxPercent AS 'Tax Percent'
    , cli.TaxID AS 'Tax ID'
    , CASE WHEN c.CheckStatus = 2 THEN 'Voided' ELSE '' END AS 'Check Void Status'
		, CASE WHEN cli.FoodStatusId = 4 THEN 'Voided' ELSE '' END AS 'Item Void Status'
    , ic.ItemClassID AS 'Product Class ID'
    , ic.Description AS 'Product Class Description'
    , NULL AS 'Product Class Export' -- no equivalent in eurekas
    , CASE WHEN c.CheckStatus = 0 THEN 'Open' ELSE 'Closed' END AS 'Check Status'
    , c.OpenedDate AS 'Check Opened On'
    , c.ClosedDate AS 'Check Closed On'
    , NULL AS 'Customer ID' -- unreachable
    , NULL AS 'Customer First Name' -- unreachable
    , NULL AS 'Customer Last Name' -- unreachable
    , u.UserName AS 'Created By'
FROM dbo.Checks c
LEFT OUTER JOIN dbo.Users u -- could use inner as well
    ON c.UserID = u.UserID
LEFT OUTER JOIN dbo.CheckLineItems cli
    ON cli.CheckID = c.CheckID -- checks can not have line items. (one example in Kibble)
LEFT OUTER JOIN dbo.Items i
    ON i.ItemID = cli.ItemID
LEFT OUTER JOIN dbo.ItemClasses ic
    ON i.ItemClassID = ic.ItemClassID
--LEFT OUTER JOIN dbo.ChecksLineSubItems clsi -- reference to the left join for line item sub items, if needed (not necessary for kibble's data)
--    ON clsi.CheckLineItemID = cli.CheckLineItemID
WHERE {$opened_or_closed_date} BETWEEN :start AND :end
ORDER BY {$opened_or_closed_date}
EOS;
        $conn = new Connection\ClubSpeedConnection("(local)", "RestaurantPiece");
        $data = $conn->query($sql, $this->getDateRange());
        // $eurekaData = $this->run_query($eurekaSql, $params);
        // $data = array_merge($data, $eurekaData); // append eureka data
        // // we could maybe hack apart the query above to get a union rolling,
        // // but we would have to conditionally wrap in a subquery/cte and then wrap that.
        // // for now, sort in memory unless efficiency / data size becomes a problem.
        // if ($opened_or_closed_date === 'c.OpenedDate')
        //     $key = 'Check Opened On';
        // else
        //     $key = 'Check Closed On';
        // usort($data, function($a, $b) use ($key) {
        //     if ($a[$key] == $b[$key])
        //         return 0; // php order of operations stupidity. don't add this to the ternary below, unless we like adding parentheses everywhere
        //     return ($a[$key] < $b[$key] ? -1 : 1);
        // });

        return $data;
    }

    public function report() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        if(empty($_GET['period']) && !in_array($_GET['period'], array('t', 'y', 'w', 'm'))) throw new RestException(412,'Invalid period supplied');

        switch($_GET['period']) {
                case 't':
                    $start = date("n/j/Y") . ' 12:00:00 AM';
                    break;
                case 'y':
                    $start = date("n/j/Y", strtotime('yesterday')) . ' 12:00:00 AM';
                    $end = date("n/j/Y", strtotime('yesterday')) . ' 11:59:59 PM';
                    break;
                case 'w':
                    $start = date("n/j/Y", strtotime('last Sunday')) . ' 12:00:00 AM';
                    break;
                case 'm':
                    $start = date("n/j/Y", strtotime('first day of this month')) . ' 12:00:00 AM';
                    break;
                default:
                    throw new RestException(412,'Invalid range given');
        }
        $end = isset($end) ? $end : date("n/j/Y") . ' 11:59:59 PM';

        $tsql_params[] = &$start;
        $tsql_params[] = &$end;
    
        $tsql = "SELECT SUM(CheckTotal) AS total_dollars, COUNT(*) AS total_count FROM Checks WHERE (ClosedDate BETWEEN ? AND ?)";
        $params = array(&$start, &$end);
        $rows1 = $this->run_query($tsql, $params);

        $tsql = "SELECT MAX(DATEPART(hh, ClosedDate)) AS hour, SUM(CheckTotal) AS total_dollars, COUNT(*) AS total_count FROM Checks WHERE (ClosedDate BETWEEN ? AND ?) GROUP BY DATEPART(hh, ClosedDate) ORDER BY hour";
        $params = array(&$start, &$end);
        $rows2 = $this->run_query($tsql, $params);
        
        $tsql = "SELECT COUNT(HeatDetails.FirstTime) AS total_racers FROM HeatDetails LEFT OUTER JOIN HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo WHERE (HeatMain.ScheduledTime BETWEEN ? AND ?)";
        $params = array(&$start, &$end);
        $rows3 = $this->run_query($tsql, $params);
        
        $tsql = "SELECT MAX(DATEPART(hh, HeatMain.ScheduledTime)) AS hour, COUNT(HeatDetails.FirstTime) AS total_racers FROM HeatDetails LEFT OUTER JOIN HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo WHERE (HeatMain.ScheduledTime BETWEEN ? AND ?) GROUP BY DATEPART(hh, HeatMain.ScheduledTime) ORDER BY hour";
        $params = array(&$start, &$end);
        $rows4 = $this->run_query($tsql, $params);
        
        $tsql = "SELECT     COUNT(HeatDetails.FirstTime) AS total_new_racers
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN ? AND ?) AND (HeatDetails.FirstTime = 'true')";
        $params = array(&$start, &$end);
        $rows5 = $this->run_query($tsql, $params);
        
        $tsql = "SELECT      COUNT(HeatDetails.FirstTime) AS total_races
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN ? AND ?)";
        $params = array(&$start, &$end);
        $rows6 = $this->run_query($tsql, $params);
        
        $tsql = "SELECT     COUNT(HeatDetails.FirstTime) AS total_races
FROM         HeatDetails LEFT OUTER JOIN
                      HeatMain ON HeatMain.HeatNo = HeatDetails.HeatNo
WHERE     (HeatMain.ScheduledTime BETWEEN ? AND ?)";
        $params = array(&$start, &$end);
        $rows7 = $this->run_query($tsql, $params);
        
        $rows1[0]['average_sale'] = empty($rows1[0]['total_count']) ? 0 : number_format(round($rows1[0]['total_dollars'] / $rows1[0]['total_count'], 2));
        $rows1[0]['total_dollars'] = number_format($rows1[0]['total_dollars']);
        $rows5[0]['percent_new_racers'] = empty($rows3[0]['total_racers']) ? 0 : round($rows5[0]['total_new_racers'] / $rows3[0]['total_racers']*100);
        
        $rph = array();
        $sph = array();
        foreach($rows2 as $row) {
            $sph[] = array($row['hour'], $row['total_dollars']);
        }
        foreach($rows4 as $row) {
            $rph[] = array($row['hour'], $row['total_racers']);
        }
        
        return array('start' => $start, 'end' => $end, 'report' => array('total_sales' => $rows1[0], 'sales_per_hour' => $sph, 'total_racers' => $rows3[0], 'racers_per_hour' => $rph, 'new_racers' => $rows5[0], 'top_items' => array_slice($rows6, 0, 5), 'total_races' => $rows7[0]));
    }   
    
    public function qb_invoices() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT     c.CheckID, cd.ProductID, cd.ProductName, c.OpenedDate, c.ClosedDate, cd.CreatedDate, c.Discount, c.CheckTotal, cd.UnitPrice, cd.UnitPrice2, cd.DiscountApplied, cd.TaxID, 
                      cd.TaxPercent, pt.TypeDescription, p.CardType
FROM         CheckDetails AS cd LEFT OUTER JOIN
                      Checks AS c ON c.CheckID = cd.CheckID LEFT OUTER JOIN
                      Payment AS p ON p.CheckID = c.CheckID LEFT OUTER JOIN
                      PayType AS pt ON pt.ID = p.PayType
WHERE     cd.ProductID IN (388, 424, 425, 426, 429) ORDER BY c.ClosedDate"; //AND (c.ClosedDate BETWEEN '10/10/2012 00:00:00' AND '10/11/2015 23:59:59')
        $params = array();
        $results = $this->run_query($tsql, $params);
        $csv = '';
        
        //die(print_r(array_keys($results[0])));
        
        $headers = array_keys($results[0]);
        $csv .= implode(',', $headers) . "\r\n";
        
        foreach($results as $key => $row) {
            foreach($row as $pos => $item){
                if(in_array($pos, array('OpenedDate', 'ClosedDate', 'CreatedDate'))) {
                    //print('match on ' . $pos . '<br/>');
                    //$row[$pos] = '"=' . $row[$pos] . '"';
                    $row[$pos] = date('Y-m-d H:i:s', strtotime($row[$pos]));
                } else {
                    $item = str_replace('"','""',$item);
                    $row[$pos] = '"' . $item . '"';
                }
            }
            $csv .= implode(',', $row) . "\r\n";
        }
        //set appropriate headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=ClubSpeed-Invoice-Details-'.date('Y-m-d-H-i').'.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        //header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();

        //read the file from disk and output the content.
        echo $csv;
        exit;
    }
    
    public function potential_revenue() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "select CONVERT(VARCHAR(10),c.OpenedDate, 102) AS date, SUM((cd.UnitPrice * cd.Qty) - cd.DiscountApplied) as total, pc.ExportName as revenueClass from Checks c LEFT JOIN CheckDetails cd ON c.CheckID = cd.CheckID LEFT JOIN Products p ON p.ProductID = cd.ProductID LEFT JOIN ProductClasses pc ON p.ProductClassID = pc.ProductClassID where c.OpenedDate BETWEEN '01-01-2014 00:00:00' AND '01-01-2014 23:59:59' AND cd.Status = 1 OR cd.Status = 3 group by c.OpenedDate, pc.ExportName ORDER BY c.OpenedDate, pc.ExportName ";
        $params = array();
        $results = $this->run_query($tsql, $params);
        $csv = '';
        
        $headers = array_keys($results[0]);
        $csv .= implode(',', $headers) . "\r\n";
            
        $output     = array();
        $categories = array();
        
        // Create an array for each category/date
        $total = 0;
        foreach($results as $row) {
            if($row['date'] == '2014.01.01') {
                $categories[$row['revenueClass']][] = $row['total'];
                $total += $row['total'];
            }
        }
        
        echo $total;
        print_r($categories);
        die();
        
        foreach($results as $key => $row) {
            $date = $row['date'];
            
            // If it's not in the date array, store it
            if(empty($output[$date])) {
                $dates[] = $date;
                $output[$date] = array();
            }
            
            $category = $row['revenueClass'];
            
            // If it's not in the category array, store it
            if(!in_array($category, $categories)) {
                $categories[] = $category;
                $output[$date][$category] = 0;
            }
            
            @$output[$date][$category] += @str_replace(',', '.', $row['total']);
            
        }
        
        $csv = array();
        
        // Add headers
        $csv[0][] = '';
        foreach($categories as $category) {
            $csv[0][] = $category;
        }
        $csv[0][] = 'Dag totaal';
        
        // Create footer
        $footer = array(0);
        
        // Add data rows
        foreach($dates as $key => $date) {
            $csv[$date]['date'] = $date;
            $dailyTotal = 0;
            foreach($categories as $category) {
                @$csv[$date][$category] = @$output[$date][$category];
                @$dailyTotal += @$output[$date][$category];
                @$footer[$category] += @$output[$date][$category];
            }
            $csv[$date][] = $dailyTotal;
        }
        
        // Create the footer totals row
        $footerTotal = 0;
        foreach($footer as $categoryTotal) {
            $footerTotal += $categoryTotal;
        }
        $footer[0] = 'Totaal';
        $footer[] = $footerTotal;
        $csv['footer'] = $footer;
        
        $csvOutput = '';
        foreach($csv as $row) {
            $csvOutput .= implode(',', $row) . "\r\n";
        }
        
        //set appropriate headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=ClubSpeed-Potential-Revenue-'.date('d-m-Y-H-i').'.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        //header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();

        //read the file from disk and output the content.
        echo $csvOutput;
        exit;
    }
    
    public function products() {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        $tsql = "SELECT p.* , c.Description AS ProductClassName
FROM         Products AS p LEFT OUTER JOIN
                      ProductClasses AS c ON c.ProductClassID = p.ProductClassID
WHERE     (p.Deleted = 'False') AND (p.Enabled = 'True')
ORDER BY p.Description";
        $params = array();
        $results = $this->run_query($tsql, $params);
        foreach($results as $key => $result) {
            //$results[$key]['icon'] = '<img src="data:image/gif;base64,' . base64_encode($results[0]['LargeIcon']) . '" border=0 width=64 height=64 align=absmiddle />';
            $results[$key]['icon'] = base64_encode($results[0]['LargeIcon']);
        }
        //die('<img src="data:image/gif;base64,' . $base_64 . '" />');
        
        if(isset($_GET['format']) && $_GET['format'] == 'datatable') {
            $jsonData = array('aaData' => array());
            foreach($results AS $row){
                $entry = array(
                    'Icon' => $row['icon'],
                    'Description'=>$row['Description'],
                    'Price1'=>'$'.number_format($row['Price1'],2),
                    'Enabled'=>$row['Enabled'],
                    'ProductClassName'=>$row['ProductClassName'],
                    'ProductID'=>$row['ProductID'],
                    
                    );
                $jsonData['aaData'][] = $entry;
            }
        }
        else {
            $jsonData = array('page'=>1,'total'=>count($results),'rows'=>array());
            foreach($results AS $row){
                $entry = array('id'=>$row['ProductID'],
                    'cell'=>$row
                    );
                $jsonData['rows'][] = $entry;
            }
        }
        return $jsonData;
    }

    /**
    * @URL GET /gift_card_balance
    */
    public function gift_card_balance($request_data) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            if (!isset($request_data['cards']) || empty($request_data['cards']))
                throw new \CSException('Cards must be provided to the gift card balance report!');
            $cards = $request_data['cards'];
            $range = Strings::rangeToCSV($cards);
            $data = $this->logic->giftCardBalance->find('CrdID IN ' . $range);
            $mapper = new \ClubSpeed\Mappers\GiftCardBalanceMapper();
            $out = $mapper->out($data);
            return $out['cards'];
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }

    /**
    * @URL GET /gift_card_transactions
    */
    public function gift_card_transactions($request_data) {
        if (!\ClubSpeed\Security\Authenticate::privateAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }
        try {
            if (!isset($request_data['cards']) || empty($request_data['cards']))
                throw new \CSException('Cards must be provided to the gift card transactions report!');
            $cards = $request_data['cards'];
            $range = Strings::rangeToCSV($cards);
            $data = $this->logic->giftCardTransactions->find('CrdID IN ' . $range);
            $mapper = new \ClubSpeed\Mappers\GiftCardTransactionsMapper();
            $out = $mapper->out($data);
            return $out['transactions'];
        }
        catch (RestException $e) {
            throw $e;
        }
        catch (CSException $e) {
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        }
        catch (Exception $e) {
            throw new RestException(500, $e->getMessage());
        }
    }
    
    private function run_query($tsql, $params = array()) {
        
        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            
            // Prepare statement
            $stmt = $conn->prepare($tsql);
    
            // Execute statement
            $stmt->execute($params);
            
            // Put in array

            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) { 
            die( print_r( $e->getMessage() ) ); 
        }
        
        return $output;
    }
}