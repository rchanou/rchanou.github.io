<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class DisabledController
 *
 * This controller just creates the "website disabled" view.
 */
class DisabledController extends BaseController
{
    public function entry()
    {
        $settings = Session::get('settings');
        $view = '/errorpages/disabled';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/errorpages/disabled-responsive';
        }

        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'strings' => Strings::getStrings(),
                'errorInfo' => json_encode(var_export(Session::get('errorInfo'),true))
            )
        );
    }
}