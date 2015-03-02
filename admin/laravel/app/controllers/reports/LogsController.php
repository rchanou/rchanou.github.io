<?php

require_once(app_path().'/includes/includes.php');

class LogsController extends BaseController
{
		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        return View::make('/screens/reports/logs',
            array(
							'controller' => 'LogsController',
            )
				);
    }

		public function data()
		{
				$params = Input::get();
				$params['model'] = 'logs';

				$data = CS_API::getDataTableData($params);

				return $data;
		}

}
