<?php

require_once(app_path().'/includes/includes.php');

class BaseController extends Controller {


    public function __construct()
    {
        $this->beforeFilter('checkIfDisabled'); //Checks if online registration is globally disabled
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
