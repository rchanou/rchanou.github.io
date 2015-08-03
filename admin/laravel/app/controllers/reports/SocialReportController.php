<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class SocialReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $defaultSocialReport = CS_API::getReport_Social();
        Session::put('mostRecentReport_Social', $defaultSocialReport);

        return View::make('/screens/reports/social',
            array('controller' => 'ReportsController',
                  'report' => $defaultSocialReport
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_Social'))
        {
            return Redirect::to('/reports/social');
        }

        $dataToExport = Session::get('mostRecentReport_Social');

        Exports::toCSV($dataToExport,'Social Media Usage Report');

    }

}