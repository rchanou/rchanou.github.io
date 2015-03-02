<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class BrokersSummaryReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
        $end = Input::get('end');

        $defaultPaymentsReport = CS_API::getReport_BrokersSummary($start,$end);
        Session::put('mostRecentReport_BrokersSummary',$defaultPaymentsReport);

        return View::make('/screens/reports/brokers/brokers-summary',
            array('controller' => 'ReportsController',
                  'report' => $defaultPaymentsReport,
                  'start' => $start,
                  'end' => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_BrokersSummary'))
        {
            return Redirect::to('/reports/brokers-summary');
        }

        $dataToExport = Session::get('mostRecentReport_BrokersSummary');

        Exports::toCSV($dataToExport,'Brokers Summary Report');

    }

}