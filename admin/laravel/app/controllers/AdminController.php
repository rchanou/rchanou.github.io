<?php

class AdminController extends BaseController //TODO: Rename to DashboardController
{

    public function dashboard()
    {
        return View::make('/screens/dashboard',array());
    }

}