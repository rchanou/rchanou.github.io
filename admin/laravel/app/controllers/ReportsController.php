<?php

require_once(app_path().'/includes/includes.php');

class ReportsController extends BaseController
{
		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $serverHasEurekas = CS_API::doesServerHaveEurekas();

        return View::make('/screens/reports/index',
            array('controller' => 'ReportsController',
                  'serverHasEurekas' => $serverHasEurekas,
									'user' => strtolower(Session::get('user'))
            ));
    }
		
		public function overview()
    {
        // TODO default to today
				$start = ''; //Input::get('start');
        $end   = ''; //Input::get('end');

        //$defaultPaymentsReport = CS_API::getReport_Payments($start,$end);

        return View::make('/screens/reports/overview',
            array('controller' => 'ReportsController',
									'start'      => $start,
                  'end'        => $end,
									'user'       => strtolower(Session::get('user'))
            ));
    }

}