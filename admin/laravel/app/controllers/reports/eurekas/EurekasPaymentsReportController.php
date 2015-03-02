<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class EurekasPaymentsReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
        $end = Input::get('end');

        $defaultPaymentsReport = CS_API::getReport_EurekasPayments($start,$end);
        Session::put('mostRecentReport_EurekasPayments',$defaultPaymentsReport);

        return View::make('/screens/reports/eurekas/payments',
            array('controller' => 'ReportsController',
                  'report' => $defaultPaymentsReport,
                  'start' => $start,
                  'end' => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_EurekasPayments'))
        {
            return Redirect::to('/reports/eurekas-payments');
        }

        $dataToExport = Session::get('mostRecentReport_EurekasPayments');

        Exports::toCSV($dataToExport,'Detailed Payments Report - Eurekas');

    }

}