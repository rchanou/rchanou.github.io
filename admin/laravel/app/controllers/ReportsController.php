<?php

require_once(app_path().'/includes/includes.php');

class ReportsController extends BaseController
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

        $serverHasEurekas = CS_API::doesServerHaveEurekas();

        return View::make('/screens/reports/index',
            array('controller' => 'ReportsController',
                  'serverHasEurekas' => $serverHasEurekas
            ));
    }

}