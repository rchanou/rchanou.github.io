<?php

require_once(app_path().'/tools/CS_API.php');
require_once(app_path().'/config/resources/strings.php');
require_once(app_path().'/config/resources/images.php');
require_once(app_path().'/config/resources/settings.php');

class Step4Controller extends BaseController {

    //##################################
    //# STEP 4 - REGISTRATION COMPLETE #
    //##################################

    /**
     * Step 4 displays a Registration Complete message, and offers a link back to the first page for future registrants.
     *
     * Step 4 flow:
     * - Session validity and language changes are checked.
     * - The view is created.
     * @return mixed
     */
    public function step4()
    {
        CS_API::checkForLanguageChange();

        if(CS_API::sessionIsInvalid() || !Session::has("signatureAcquired"))
        {
            return Redirect::to(CS_API::getStep1URL());
        }

        Session::put("sessionComplete", true);

        $session = Session::all();
        return View::make('/steps/step4', array('strings' => $session['strings'],
            'images' => $session['images'],
            'settings' => $session['settings'],
            'translations' => $session['translations'],
            'currentCulture' => $session['currentCulture'],
            'currentCultureFB' => $session['currentCultureFB'],
            'step1URL' => CS_API::getStep1URL()) );
    }

} 