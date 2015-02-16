<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class BrokerCodesReportController extends BaseController
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

        $report = CS_API::getReport_BrokerCodes($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_BrokerCodes',$report);

        return View::make('/screens/reports/broker-codes',
            array('controller' => 'ReportsController',
                  'report' => $report,
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_BrokerCodes'))
        {
            return Redirect::to('/reports/broker-codes');
        }

        $dataToExport = Session::get('mostRecentReport_BrokerCodes');

        Exports::toCSV($dataToExport,'Broker/Affiliate Codes Report');

    }

}