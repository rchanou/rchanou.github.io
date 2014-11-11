<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class DisconnectedController
 *
 * This controller just creates the "disconnected" error page.
 * It often outputs useful debugging information to the JavaScript console.
 */
class DisconnectedController extends BaseController
{
    public function entry()
    {
        try
        {
            return View::make('/errorpages/disconnected',
                array(
                    'images' => Images::getImageAssets(),
                    'errorInfo' => json_encode(Session::get('errorInfo'))
                )
            );
        }
        catch(Exception $e)
        {
            return View::make('/errorpages/disconnected',
                array(
                    'images' => Images::getImageAssets(),
                    'errorInfo' => Session::get('errorInfo')
                )
            );
        }

    }
}