<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class AccountingReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
				$end   = Input::get('end');
				
				$start = empty($start) ? date('Y-m-d') : $start;
        $end   = empty($end)   ? date('Y-m-d') : $end;

        $report = CS_API::getReport_Accounting($start, $end);

        Session::put('mostRecentReport_Accounting', $report);

				// Format for view
				$total_debits = $total_credits = 0;
				//print_r($report);die();
				foreach($report as $line) {
					//print_r($line);die();
					$total_debits  += $line->Debit;
					$total_credits += $line->Credit;
				}
				//echo $total_debits;
				//echo $total_credits;
				//die();

        return View::make('/screens/reports/accounting',
            array('controller'    => 'ReportsController',
                  'report'        => $report,
                  'start'         => $start,
                  'end'           => $end,
                  'total_debits'  => $total_debits,
									'total_credits' => $total_credits
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toCSV($dataToExport, 'Accounting Export');

    }
		
		public function exportToIIF()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toIIF($dataToExport, 'Accounting Export');

    }
		
		public function exportToSAGE()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toSAGE($dataToExport, 'Accounting Export');

    }

}