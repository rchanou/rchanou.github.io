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
        //TODO: Enhance with error codes.

        return View::make('/errorpages/disconnected',
            array(
                'images' => Images::getImageAssets(),
                'errorInfo' => json_encode(Session::get('errorInfo'))
            )
        );
    }
}