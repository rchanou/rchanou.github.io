<?php

require_once(app_path().'/includes/includes.php');

class DisconnectedController extends BaseController
{
    public function entry()
    {
        return View::make('/errorpages/disconnected',
            array(
                'images' => Images::getImageAssets(),
                'errorInfo' => json_encode(Session::get('errorInfo'))
            )
        );
    }
}