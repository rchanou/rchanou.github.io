<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class EurekasDetailedSalesReportController extends BaseController
{

    public function index()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        $start = Input::get('start');
        $end = Input::get('end');
        $show_by_opened_date = Input::get('show_by_opened_date');

        $report = CS_API::getReport_EurekasDetailedSales($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_EurekasDetailedSales',$report);

        return View::make('/screens/reports/eurekas/detailed-sales',
            array('controller' => 'ReportsController',
                  'report' => $report,
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_EurekasDetailedSales'))
        {
            return Redirect::to('/reports/eurekas-detailed-sales');
        }

        $dataToExport = Session::get('mostRecentReport_EurekasDetailedSales');

        Exports::toCSV($dataToExport,'Detailed Sales Report - Eurekas');

    }

}