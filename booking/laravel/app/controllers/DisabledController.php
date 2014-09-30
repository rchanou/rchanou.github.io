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
        return View::make('/errorpages/disabled',
            array(
                'images' => Images::getImageAssets()
            )
        );
    }
}