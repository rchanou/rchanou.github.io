<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class EurekasSummaryPaymentsReportController extends BaseController
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

        $defaultPaymentsReport = CS_API::getReport_EurekasSummaryPayments($start,$end);

        Session::put('mostRecentReport_EurekasSummaryPayments',$defaultPaymentsReport);

        return View::make('/screens/reports/eurekas/summary-payments',
            array('controller' => 'ReportsController',
                  'report' => $defaultPaymentsReport,
                  'start' => $start,
                  'end' => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_EurekasSummaryPayments'))
        {
            return Redirect::to('/reports/eurekas-summary-payments');
        }

        $dataToExport = Session::get('mostRecentReport_EurekasSummaryPayments');

        Exports::toCSV($dataToExport,'Summary Payments Report - Eurekas');

    }

}