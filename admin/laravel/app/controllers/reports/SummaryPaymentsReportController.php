<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class SummaryPaymentsReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
        $end = Input::get('end');

        $defaultPaymentsReport = CS_API::getReport_SummaryPayments($start,$end);

        Session::put('mostRecentReport_SummaryPayments',$defaultPaymentsReport);

        return View::make('/screens/reports/summary-payments',
            array('controller' => 'ReportsController',
                  'report' => $defaultPaymentsReport,
                  'start' => $start,
                  'end' => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_SummaryPayments'))
        {
            return Redirect::to('/reports/summary-payments');
        }

        $dataToExport = Session::get('mostRecentReport_SummaryPayments');

        Exports::toCSV($dataToExport,'Summary Payments Report');

    }

}