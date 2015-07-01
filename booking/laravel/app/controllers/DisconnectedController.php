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
        $mostRecentAPICallResult = 'None!';

        if (Session::has('callInfo'))
        {
            $mostRecentAPICallResult = json_encode(var_export(Session::get('callInfo'),true));
        }
        CS_API::log('ERROR :: Online Booking user reached disconnected page. Most recent API call results: ' . $mostRecentAPICallResult);

        return View::make('/errorpages/disconnected',
            array(
                'images' => Images::getImageAssets(),
                'errorInfo' => json_encode(var_export(Session::get('errorInfo'),true)),
                'strings' => Strings::getStrings()
            )
        );
    }
}