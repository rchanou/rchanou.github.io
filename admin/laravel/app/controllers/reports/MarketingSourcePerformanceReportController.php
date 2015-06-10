<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class MarketingSourcePerformanceReportController extends BaseController
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

        $report = CS_API::getReport_MarketingSourcePerformance($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_MarketingSourcePerformance',$report);

        return View::make('/screens/reports/marketing-source-performance',
            array('controller' => 'ReportsController',
                  'report' => $report,
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_MarketingSourcePerformance'))
        {
            return Redirect::to('/reports/marketing-source-performance');
        }

        $dataToExport = Session::get('mostRecentReport_MarketingSourcePerformance');

        Exports::toCSV($dataToExport,'Marketing Source Performance Report');

    }

}