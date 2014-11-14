<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class DetailedSalesReportController extends BaseController
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

        $report = CS_API::getReport_DetailedSales($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_DetailedSales',$report);

        return View::make('/screens/reports/detailed-sales',
            array('controller' => 'ReportsController',
                  'report' => $report,
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_DetailedSales'))
        {
            return Redirect::to('/reports/detailed-sales');
        }

        $dataToExport = Session::get('mostRecentReport_DetailedSales');

        Exports::toCSV($dataToExport,'Detailed Sales Report');

    }

}