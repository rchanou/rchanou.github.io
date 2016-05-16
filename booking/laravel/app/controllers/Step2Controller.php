<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class Step2Controller
 * View: step2.blade.php
 * URL: /step2
 *
 * Description:
 * This controller handles the entirety of the "Choose a Race" page, including searching, account creation, login, and
 * adding to cart.
 */

class Step2Controller extends BaseController
{
    /**
     * This function handles entry into Step 2 (Choose a Race) of Online Booking, whether or not a GET or POST occurred.
     * If it receives a start date, heatType, and numberOfParticipants (either POST or GET), it searches for
     * available bookings matching that criteria. If any of those values are missing, it either defaults to the last
     * search or redirects with an error as appropriate.
     *
     * It then renders the view which allows the user to create an account or login in order to add a specific heat
     * to their cart.
     * @return mixed
     */
    public function entry()
    {
        $strings = Strings::getStrings();
        $settings = Settings::getSettings(true); //Force a refresh of all settings
        Session::put('settings',$settings);
        checkForCultureChange();

        //Gather up any GET or POST inputs for the race search
        $start = Input::get('start');
        $numberOfParticipants = Input::get('numberOfParticipants');
        if ($numberOfParticipants == null) {$numberOfParticipants = 1;} //Default number of racers to 1 if missing
        $heatType = Input::get('heatType');

        //If the start date is missing and we have no record of a prior search, redirect to the first page with an error
        if ($start === null)
        {
            $lastSearch = Session::get('lastSearch');
            if ($lastSearch == null)
            {
                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_pleaseSelectARaceDate']);
                return Redirect::to('/step1')->withErrors($messages)->withInput();
            }
            else //If there was a prior search, use that data
            {
                $start = $lastSearch['start'];
                $numberOfParticipants = $lastSearch['numberOfParticipants'];
                $heatType = $lastSearch['heatType'];
            }
        }

        //Remember the most recent search
        Session::put('lastSearch',array('start'=>$start,'numberOfParticipants'=>$numberOfParticipants,'heatType'=>$heatType));

        //Determine today's date
        $dateFormat = $settings['dateDisplayFormat'];
        $currentDateTime = new DateTime();
        $today = $currentDateTime->format($dateFormat);

        if ($start == $today) //If we're searching for today's results, only get the ones from Club Speed that haven't happened yet
        {
            $races = CS_API::getAvailableBookings();
        }
        else //If not searching for today's results, just list all results JUST for that day
        {
            $endDate = $start . 'T23:59:59';
            $races = CS_API::getAvailableBookings($start,$endDate);
        }
        $this->recordProductInfo($races); //Remember every race and its details and store them in the session

        $races = CS_API::filterHeatsByAvailableSpots($races,$numberOfParticipants); //Only list the results with at least one spot available
        $races = CS_API::filterHeatsByHeatType($races,$heatType);

        if ($races === null) //If there was an error with the API call, redirect to the Disconnected page
        {
            return Redirect::to('/disconnected');
        }

        //Used to generate links for easy date navigation
        $startDateTime = new DateTime($start);
        $previousDay = $startDateTime;
        $previousDay = $previousDay->modify('-1 day')->format('Y-m-d');
        $previousDayDisplay = new DateTime($start);
        $previousDayDisplay = $previousDayDisplay->modify('-1 day')->format($dateFormat);
        $startDateTime = new DateTime($start);
        $nextDay = $startDateTime;
        $nextDay = $nextDay->modify('+1 day')->format('Y-m-d');
        $nextDayDisplay = new DateTime($start);
        $nextDayDisplay = $nextDayDisplay->modify('+1 day')->format($dateFormat);

        $settings = Session::get('settings');
        $locale = $settings['numberFormattingLocale'];
        $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::CURRENCY);
        $currency = $settings['currency'];

        //Render the page
        $view = '/steps/step2';
        if (isset($settings['responsive']) && $settings['responsive'] == true)
        {
            $view = '/steps/step2-responsive';
        }
        return View::make($view,
            array(
                'images' => Images::getImageAssets(),
                'races' => $races,
                'start' => $start,
                'previousDay' => $previousDay,
                'previousDayDisplay' => $previousDayDisplay,
                'nextDayDisplay' => $nextDayDisplay,
                'nextDay' => $nextDay,
                'heatType' => $heatType,
                'numberOfParticipants' => $numberOfParticipants,
                'authenticated' => Session::get('authenticated'),
                'loginToAccountErrors' => Session::get('loginToAccountErrors'),
                'createAccountErrors' => Session::get('createAccountErrors'),
                'settings' => $settings,
                'strings' => Strings::getStrings(),
                'moneyFormatter' => $moneyFormatter,
                'currency' => $currency
            )
        );
    }

    /**
     * This function stores in the session every available product and its details.
     * The cart will use this to render things like a product name, price, and so on.
     *
     * @param $products
     */
    public function recordProductInfo($products)
    {
        $currentProductInfo = Session::get('productInfo');
        if ($currentProductInfo == null) { $currentProductInfo = array(); }
        $productInfo = $currentProductInfo;
        if ($products != null)
        {
            foreach($products as $currentProduct)
            {
                $productInfo[$currentProduct->heatId] = $currentProduct;
            }
        }
        Session::put('productInfo',$productInfo);
    }
}