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
									'user' => Session::get('user')
            ));
    }

}