<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class EventRepSalesReportController extends BaseController
{
		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
        $end = Input::get('end');
        $show_by_opened_date = Input::get('show_by_opened_date');

        $report = CS_API::getReport_EventRepSales($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_EventRepSales',$report);

        return View::make('/screens/reports/event-rep-sales',
            array('controller' => 'ReportsController',
                  'report' => $report,
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_EventRepSales'))
        {
            return Redirect::to('/reports/event-rep-sales');
        }

        $dataToExport = Session::get('mostRecentReport_EventRepSales');

        Exports::toCSV($dataToExport,'Event Rep Sales Report');

    }

}