<?php

class AdminController extends BaseController //TODO: Rename to DashboardController
{

    public function dashboard()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }
        return View::make('/screens/dashboard',array());
    }

}