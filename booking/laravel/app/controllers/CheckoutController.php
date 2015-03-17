<?php

/**
 * Class CheckoutController
 *
 * This class handles the entirety of the logic for checkout.
 */
class CheckoutController extends BaseController
{
    /**
     * This just renders the initial checkout page if there is a cart with at least one item in it.
     *
     * @return mixed
     */
    public function entry()
    {
        if(!Session::has('cart') || count(Session::get('cart')) == 0 || !Session::has('authenticated'))
        {
            return Redirect::to('/cart');
        }

        $cart = Session::get('cart');
        $localCartHasExpiredItem = false;

        //Get the current online reservations from Club Speed
        $currentOnlineReservations = CS_API::getOnlineReservations();
        if ($currentOnlineReservations === false || $currentOnlineReservations === null)
        {
            return Redirect::to('/disconnected');
        }
        $listOfRemoteOnlineBookingReservationIds = array();
        foreach($currentOnlineReservations as $currentOnlineBookingReservation)
        {
            $listOfRemoteOnlineBookingReservationIds[] = $currentOnlineBookingReservation->onlineBookingReservationsId;
        }

        //See if any items in the local cart have been deleted from the Club Speed server
        foreach($cart as $cartItemId => $cartItem)
        {
            if ($cartItem['type'] == 'heat')
            {
                if (!isset($cartItem['onlineBookingsReservationId']) || !in_array($cartItem['onlineBookingsReservationId'], $listOfRemoteOnlineBookingReservationIds)) //If the local item is out of sync
                {
                    $localCartHasExpiredItem = true;
                    Cart::removeFromCart($cartItemId); //Then remove it from the local cart
                }
            }
        }

        if ($localCartHasExpiredItem)
        {
            Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.
        }

        //Format the current items in the cart for the Virtual Check API call
        $checkDetails = array('checks' => array());
        $checkDetails['checks'][0] = array('details' => array());
        foreach($cart as $cartItemIndex => $cartItem)
        {
            $newItem = array();
            if ($cartItem['type'] == 'heat')
            {
                $newItem['productId'] = $cartItem['data']->products[0]->productsId;
                $newItem['qty'] = $cartItem['quantity'];
                $newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
            if ($cartItem['type'] == 'product')
            {
                $newItem['productId'] = $cartItem['itemId'];
                $newItem['qty'] = $cartItem['quantity'];
                $newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
        }

        //Check the Virtual Check calculation from Club Speed
        $check = CS_API::getVirtualCheck($checkDetails);
        if ($check === null || !property_exists($check,'checks'))
        {
            return Redirect::to('/disconnected');
        }

        //Package the Virtual Check data for the view
        $virtualCheckDetails = array();
        $virtualCheck = array();
        if (count($check->checks) > 0)
        {
            $virtualCheck = $check->checks[0];
            foreach($check->checks[0]->details as $currentCheckDetail)
            {
                $virtualCheckDetails[$currentCheckDetail->checkDetailId] = $currentCheckDetail;
            }
        }

        $settings = Session::get('settings');
        $locale = $settings['numberFormattingLocale'];
        $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::CURRENCY);
        $currency = $settings['currency'];

        $heatItems = 0;
        $nonHeatItems = 0;
        foreach($cart as $cartItemId => $cartItem)
        {
            if ($cartItem['type'] == 'heat')
            {
                $heatItems++;
            }
            else
            {
                $nonHeatItems++;
            }
        }

        $countries = CS_API::getCountries();

        $countries = is_array($countries) ? $countries : null;
        $strings = Strings::getStrings();
        if ($countries != null)
        {
            $countriesFormatted = array();
            $countriesFormatted['ZZ'] = $strings['str_pleaseSelectAnOption'];
            foreach($countries as $country) {
                $countriesFormatted[$country->{'ISO_3166-1_Alpha_2'}] = $country->Name;
            }
        }
        else
        {
            $countriesFormatted = null;
        }

        return View::make('/checkout',
            array(
                'images' => Images::getImageAssets(),
                'localCartHasExpiredItem' => $localCartHasExpiredItem,
                'virtualCheck' => $virtualCheck,
                'virtualCheckDetails' => $virtualCheckDetails,
                'cart' => $cart,
                'moneyFormatter' => $moneyFormatter,
                'currency' => $currency,
                'settings' => $settings,
                'hasHeatItems' => $heatItems > 0 ? true : false,
                'hasNonHeatItems' => $nonHeatItems > 0 ? true : false,
                'strings' => $strings,
                'countries' => $countriesFormatted
            )
        );
    }

    public function pay()
    {
        $input = Input::all();

        $strings = Strings::getStrings();
        //STEP 1: Validate input server-side

        //Data validation
        $rules = array();
        $rules['firstName'] = 'required';
        $rules['lastName'] = 'required';
        $rules['number'] = 'required';
        $rules['cvv'] = 'required';
        $rules['expiryMonth'] = 'required';
        $rules['expiryYear'] = 'required';
        $rules['address1'] = 'required';
        //rules['address2'] may be needed in the future
        $rules['city'] = 'required';
        $rules['state'] = 'required';
        $rules['postcode'] = 'required';
        $rules['country'] = 'required|not_in:ZZ';
        //$rules['phone'] may be needed in the future

        $messages = array(
            'firstName.required' => $strings['str_firstName.required'],
            'lastName.required' => $strings['str_lastName.required'],
            'number.required' => $strings['str_number.required'],
            'cvv.required' => $strings['str_cvv.required'],
            'expiryMonth.required' => $strings['str_expiryMonth.required'],
            'expiryYear.required' => $strings['str_expiryYear.required'],
            'address1.required' => $strings['str_address1.required'],
            'city.required' => $strings['str_city.required'],
            'state.required' => $strings['str_state.required'],
            'postcode.required' => $strings['str_postcode.required'],
            'country.required' => $strings['str_country.required'],
            'country.not_in' => $strings['str_country.required']
        );

        //Create the validator
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails())
        {
            return Redirect::to('/checkout')->withErrors($validator)->withInput();
        }

        //STEP 2: Check cart for expired items - if any, redirect with error
        $cart = Session::get('cart');

        //Get the current online reservations from Club Speed
        $currentOnlineReservations = CS_API::getOnlineReservations();
        if ($currentOnlineReservations === false || $currentOnlineReservations === null)
        {
            return Redirect::to('/disconnected');
        }
        $listOfRemoteOnlineBookingReservationIds = array();
        foreach($currentOnlineReservations as $currentOnlineBookingReservation)
        {
            $listOfRemoteOnlineBookingReservationIds[] = $currentOnlineBookingReservation->onlineBookingReservationsId;
        }

        $localCartHasExpiredItem = false;

        //See if any items in the local cart have been deleted from the Club Speed server
        foreach($cart as $cartItemId => $cartItem)
        {
            if ($cartItem['type'] == 'heat')
            {
                if (!isset($cartItem['onlineBookingsReservationId']) || !in_array($cartItem['onlineBookingsReservationId'], $listOfRemoteOnlineBookingReservationIds)) //If the local item is out of sync
                {
                    Cart::removeFromCart($cartItemId); //Then remove it from the local cart
                    $localCartHasExpiredItem = true;
                }
            }
        }

        if ($localCartHasExpiredItem) //If the cart had an item removed, redirect with an error and stop payment
        {
            Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.

            $cart = Session::get('cart'); //Update the cart in memory
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_oneOrMoreItemsExpiredDuringPayment']);
            if (count($cart) == 0) //If the last item in the cart was just removed
            {
                return Redirect::to('/cart')->withErrors($messages)->withInput(); //Go to cart page
            }
            else
            {
                return Redirect::to('/checkout')->withErrors($messages)->withInput(); //Go to checkout page
            }
        }

        //STEP 3: Create the real check (a new one is created for each payment attempt)

        $checkId = null;

        //Package the data in the format expected by the API
        $checkDetails = array('checks' => array());
        $checkDetails['checks'][] = array(
            'userId' => 1, //support user in Club Speed
            'customerId' => Session::get('authenticated'),
            'details' => array(),
            'broker' => Session::has('brokerName') ? Session::get('brokerName') : ''
        );

        //Format the current items in the cart for the Virtual Check API call
        foreach($cart as $cartItemIndex => $cartItem)
        {
            $newItem = array();
            if ($cartItem['type'] == 'heat')
            {
                $newItem['productId'] = $cartItem['data']->products[0]->productsId;
                $newItem['qty'] = $cartItem['quantity'];
                $newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
            if ($cartItem['type'] == 'product')
            {
                $newItem['productId'] = $cartItem['itemId'];
                $newItem['qty'] = $cartItem['quantity'];
                $newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
        }

        //Create the actual check
        $checkId = CS_API::createCheck($checkDetails);
        if ($checkId === null || !is_numeric($checkId))
        {
            return Redirect::to('/disconnected');
        }

        Session::put('checkId',$checkId);


        $check = CS_API::getCheck($checkId);
        if ($check === null || !property_exists($check,'checks'))
        {
            return Redirect::to('/disconnected');
        }

        //STEP 4: Make sure that the subtotal, tax, and total matches what the user saw in the checkout page
        $expectedSubtotal = $input['expectedSubtotal'];
        $expectedTax = $input['expectedTax'];
        $expectedTotal = $input['expectedTotal'];

        $actualSubtotal = $check->checks[0]->checkSubtotal;
        $actualTax = $check->checks[0]->checkTax;
        $actualTotal = $check->checks[0]->checkTotal;

        //TODO: Bug with tax calculations. These sanity checks need to be re-enabled
        /*
        if ($expectedSubtotal != $actualSubtotal || $expectedTax != $actualTax || $expectedTotal != $expectedTotal)
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "There was a problem validating your order. Please try again later.");
            return Redirect::to('/checkout')->withErrors($messages)->withInput();
        }
        */

        //STEP 5: Attempt the payment
        $settings = Settings::getSettings();
        $onlineBookingPaymentProcessorSettings = $settings['onlineBookingPaymentProcessorSettings'];

        $email = Session::get('authenticatedEmail');
        $paymentInformation = array(
            "firstName" => isset($input['firstName']) ? $input['firstName'] : '',
            "lastName" => isset($input['lastName']) ? $input['lastName'] : '',
            "number" => isset($input['number']) ? $input['number'] : '',
            "expiryMonth" => isset($input['expiryMonth']) ? $input['expiryMonth'] : '',
            "expiryYear" => isset($input['expiryYear']) ? $input['expiryYear'] : '',
            "startMonth" => isset($input['startMonth']) ? $input['startMonth'] : '',
            "startYear" => isset($input['startYear']) ? $input['startYear'] : '',
            "cvv" => isset($input['cvv']) ? $input['cvv'] : '',
            "issueNumber" => isset($input['issueNumber']) ? $input['issueNumber'] : '',
            "address1" => isset($input['address1']) ? $input['address1'] : '',
            "address2" => isset($input['address2']) ? $input['address2'] : '',
            "city" => isset($input['city']) ? $input['city'] : '',
            "postcode" => isset($input['postcode']) ? $input['postcode'] : '',
            "state" => isset($input['state']) ? $input['state'] : '',
            "country" => isset($input['country']) ? $input['country'] : '',
            "phone" => isset($input['phone']) ? $input['phone'] : '',
            "email" => isset($email) ? $email : ''
        );

        $checkFormatted = array('checkId' => $checkId,
                       'details' => array());

        //Format the details array for the API call
        $details = array();
        foreach($cart as $cartItemIndex => $cartItem)
        {
            $newDetailItem = array();
            if ($cartItem["type"] == "heat")
            {
                $newDetailItem["heatId"] = $cartItem["itemId"];
            }
            if ($cartItem["type"] == "heat" && $cartItem["quantity"] > 1)
            {
                $newDetailItem["additionalReservations"] = $cartItem["quantity"] - 1;
            }
            $details[] = $newDetailItem;
        }

        foreach($check->checks as $currentCheck)
        {
            $currentIndex = 0;
            foreach($currentCheck->details as $currentCheckDetail)
            {
                $details[$currentIndex]["checkDetailId"] = $currentCheckDetail->checkDetailId;
                $currentIndex++;
            }
        }

        $checkFormatted["details"] = $details;

        $result = CS_API::makePayment($onlineBookingPaymentProcessorSettings,$checkFormatted,$paymentInformation);

        if ($result === null)
        {
            return Redirect::to('/disconnected');
        }
        else if ($result === true) //STEP 6: If successful, direct to success page
        {
            $cart = Session::get('cart'); //Remove all online booking reservations
            if ($cart !== null)
            {
                foreach ($cart as $cartItemId => $cartItem)
                {
                    if ($cartItem['type'] == 'heat')
                    {
                        $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                        $result = CS_API::makeOnlineReservationPermanent($onlineBookingsReservationId); //Mark the online booking reservation and permanent
                        if ($result === null)
                        {
                            return Redirect::to('/disconnected');
                        }
                    }
                }
            }

            //Direct to success page
            $successResults = array(
                'check' => $check,
                'checkId' => $checkId,
                'paymentInformation' => $paymentInformation
            );
            return Redirect::to('/success')->with('successResults',$successResults);
        }
        else //STEP 7: If failure, redirect with error message
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_paymentDeclined']); //TODO: Enhance error messages
            return Redirect::to('/checkout')->withErrors($messages)->withInput(); //Go to checkout page
        }

    }
} 