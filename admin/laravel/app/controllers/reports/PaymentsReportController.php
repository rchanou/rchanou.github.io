<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class PaymentsReportController extends BaseController
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

        $defaultPaymentsReport = CS_API::getReport_Payments($start,$end);
        Session::put('mostRecentReport_Payments',$defaultPaymentsReport);

        return View::make('/screens/reports/payments',
            array('controller' => 'ReportsController',
                  'report' => $defaultPaymentsReport,
                  'start' => $start,
                  'end' => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_Payments'))
        {
            return Redirect::to('/reports/payments');
        }

        $dataToExport = Session::get('mostRecentReport_Payments');

        Exports::toCSV($dataToExport,'Detailed Payments Report');

    }

}