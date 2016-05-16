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
            $callInfo = Session::get('callInfo');

            //Prevent credit card information from being logged
            if (isset($callInfo["params"]->card["number"])) { $callInfo["params"]->card["number"] = "XXXXXXXXXXXX".substr($callInfo["params"]->card["number"],-4); }
            if (isset($callInfo["params"]->card["cvv"])) { $callInfo["params"]->card["cvv"] = "XXX"; }

            $mostRecentAPICallResult = json_encode(var_export($callInfo,true));
        }
        CS_API::log('ERROR :: Online Booking user reached disconnected page. Most recent API call results: ' . $mostRecentAPICallResult);

        $settings = Session::get('settings');
        $view = '/errorpages/disconnected';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/errorpages/disconnected-responsive';
        }

        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'errorInfo' => json_encode(var_export(Session::get('errorInfo'),true)),
                'strings' => Strings::getStrings()
            )
        );
    }
}