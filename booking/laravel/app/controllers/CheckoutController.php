<?php

/**
 * Class CheckoutController
 *
 * This class handles the entirety of the logic for checkout.
 */
class CheckoutController extends BaseController
{
    /**
     * Renders the checkout page if appropriate.
     *
     * If the payment processor is NOT offsite, this just renders the initial checkout page if there is a cart with at least one item in it.
     * If the payment processor IS offsite, it skips rendering the checkout view and skips to the payment step accordingly.
     *
     */
    public function entry()
    {
        //Redirect users away from Checkout if their cart is empty
        $isNotReadyForCheckout = (!Session::has('cart') || count(Session::get('cart')) == 0 || !Session::has('authenticated'));
        if($isNotReadyForCheckout)
        {
            return Redirect::to('/cart');
        }

        //Redirect to an external payment processor if needed
        $latestSettings = Settings::getSettings(true);
        $onlineBookingPaymentProcessorSettings = $latestSettings['onlineBookingPaymentProcessorSettings'];

        $isOffsiteRedirect = CS_API::getProcessorType($onlineBookingPaymentProcessorSettings->name) === 'redirect' ? true : false;
        if ($isOffsiteRedirect)
        {
            return Redirect::to('/pay/redirect')->with(array(
                'onlineBookingPaymentProcessorSettings' => $onlineBookingPaymentProcessorSettings
            ));
        }

        //Get the latest cart
        $cart = $this->getLatestCart();
        $latestCartCallFailed = ($cart === null);
        if ($latestCartCallFailed)
        {
            CS_API::log('ERROR :: Online Booking failed to fetch the latest online reservations!');
            return Redirect::to('/disconnected');
        }

        //Generate check details for virtual check API call
        $checkDetails = $this->convertCartToVirtualCheckDetails($cart);

        //Get the Virtual Check calculation from Club Speed
        $check = CS_API::getVirtualCheck($checkDetails);
        if ($check === null || !property_exists($check,'checks'))
        {
            CS_API::log('ERROR :: Online Booking failed to create a virtual check!');
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

        //Get settings and country information for the view
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

        $strings = Strings::getStrings();

        $countriesFormatted = $this->generateListOfCountries($strings);

        $localCartHasExpiredItem = false;
        if (Session::has('localCartHasExpiredItem'))
        {
            $localCartHasExpiredItem = true;
            Session::forget('localCartHasExpiredItem');
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

    /**
     * Handles standard checkout payments via Omnipay.
     *
     */
    public function pay()
    {
        $input = Input::all();
        $strings = Strings::getStrings();

        //Validate input server-side
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

        //Update the cart and check for expired items
        $cart = $this->getLatestCart();
        $latestCartCallFailed = ($cart === null);
        if ($latestCartCallFailed)
        {
            CS_API::log('ERROR :: Online Booking failed at the payment step while trying to fetch the latest online reservations!');
            return Redirect::to('/disconnected');
        }

        if (Session::has('localCartHasExpiredItem')) //If the cart had an item removed, redirect with an error and stop payment
        {
            Session::forget('localCartHasExpiredItem');

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

        //Create the real check (a new one is created for each payment attempt)
        $checkId = null;

        //Package the data in the format expected by the API
        $checkDetails = $this->convertCartToRealCheckDetails($cart);

        //Create the actual check
        $checkId = CS_API::createCheck($checkDetails);
        if ($checkId === null || !is_numeric($checkId))
        {
            CS_API::log('ERROR :: Online Booking failed at check creation step.');
            return Redirect::to('/disconnected');
        }

        Session::put('checkId',$checkId);

        //Bind the checkId to every onlineBookingReservation related to an item in the cart
        if ($cart !== null)
        {
            foreach ($cart as $cartItemId => $cartItem)
            {
                if ($cartItem['type'] == 'heat')
                {
                    $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                    $result = CS_API::bindCheckToOnlineReservation($onlineBookingsReservationId,$checkId);

                    if ($result === null)
                    {
                        CS_API::log("ERROR :: Online Booking failed to bind the checkId ($checkId) to the onlineBookingReservation ($onlineBookingsReservationId)! Customer was charged already, so proceeding as normal.");
                    }
                }
            }
        }

        //Fetch the newly created check
        $check = CS_API::getCheck($checkId);
        if ($check === null || !property_exists($check,'checks'))
        {
            CS_API::log('ERROR :: Online Booking failed at the step it tried to fetch a check.');
            return Redirect::to('/disconnected');
        }

        //Make sure that the subtotal, tax, and total matches what the user saw in the checkout page
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

        //Attempt the payment
        $settings = Settings::getSettings(true);
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
        $formattedTotalAmount = number_format($actualTotal,2,'.','');
        $onlineBookingPaymentProcessorSettings->amount = $formattedTotalAmount;

        //Added for SagePay
        $onlineBookingPaymentProcessorSettings->transactionId = $checkId;
        $onlineBookingPaymentProcessorSettings->Description = "Club Speed Online Booking Transaction - Check $checkId";

        //Added for WorldPayXML
        $onlineBookingPaymentProcessorSettings->session = Session::getId();
        $onlineBookingPaymentProcessorSettings->clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        $result = CS_API::pay($onlineBookingPaymentProcessorSettings,$checkFormatted,$paymentInformation);

        if ($result === null)
        {
            CS_API::log('ERROR :: Online Booking failed at the make payment step.');
            return Redirect::to('/disconnected');
        }

        $isRedirect = (isset($result->type) && $result->type == "redirect");
        if ($isRedirect)
        {
            CS_API::log("ERROR :: Online Booking received a redirection payment processor when it expected a direct one!");
            return Redirect::to('/disconnected');
        }


        //$paymentWasSuccessful = isset($result->success) ? $result->success : false;
        $paymentWasSuccessful = (isset($result->type) && $result->type === "success");
        $transactionId = isset($result->reference) ? $result->reference : null;

        if ($paymentWasSuccessful) //If successful
        {
            //Remove all online booking reservations
            $cart = Session::get('cart');
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
                            CS_API::log("ERROR :: Online Booking failed to make an online reservation permanent ($onlineBookingsReservationId)! Customer was charged already, so proceeding as normal.");
                        }
                    }
                }
            }

            //Insert a payment record
            $paymentInsertionSucceeded = CS_API::insertPaymentRecord($checkId,$formattedTotalAmount,$transactionId, $onlineBookingPaymentProcessorSettings->name);
            if ($paymentInsertionSucceeded)
            {
                //Finalize the check
                $checkFinalizationSucceeded = CS_API::finalizeCheck($checkId,$details);
                if (!$checkFinalizationSucceeded)
                {
                    CS_API::log("ERROR :: Online Booking failed to finalize the check! (Check ID: $checkId, Details: " . print_r($details,true) . " - Customer was charged already, so proceeding as normal.");
                }
            }
            else
            {
                CS_API::log("ERROR :: Online Booking failed to insert a payment record! (Check ID: $checkId, Total: $formattedTotalAmount, Transaction: $transactionId, Details: " . print_r($details,true) . ") Customer was charged already, so proceeding as normal, but skipping check finalization.");
            }

            //Direct to success page
            $successResults = array(
                'check' => $check,
                'checkId' => $checkId,
                'paymentInformation' => $paymentInformation
            );
            return Redirect::to('/success')->with('successResults',$successResults);
        }
        else //If failure, redirect with error message
        {
            CS_API::log("INFO :: Online Booking - Payment was declined for check $checkId - voiding the check");

            $checkWasVoidedSuccessfully = CS_API::voidCheck($checkId);
            if (!$checkWasVoidedSuccessfully)
            {
                CS_API::log("ERROR :: Online Booking - Check $checkId could not be voided!");
            }

            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_paymentDeclined']);
            return Redirect::to('/checkout')->withErrors($messages)->withInput(); //Go to checkout page
        }

    }

    /**
     * Handles redirecting to offsite payment processors.
     *
     */
    public function payRedirect()
    {
        $strings = Strings::getStrings();

        if (Session::has('onlineBookingPaymentProcessorSettings'))
        {
            Session::put('redirectionInProgress',true);

            //Update the cart and check for expired items
            $cart = $this->getLatestCart();
            $latestCartCallFailed = ($cart === null);
            if ($latestCartCallFailed)
            {
                CS_API::log('ERROR :: Online Booking failed at the payment step while trying to fetch the latest online reservations!');
                return Redirect::to('/disconnected');
            }

            if (Session::has('localCartHasExpiredItem')) //If the cart had an item removed, redirect with an error and stop payment
            {
                Session::forget('localCartHasExpiredItem');

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

            //Create the real check (a new one is created for each payment attempt)
            $checkId = null;

            //Package the data in the format expected by the API
            $checkDetails = $this->convertCartToRealCheckDetails($cart);

            //Create the actual check
            $checkId = CS_API::createCheck($checkDetails);
            if ($checkId === null || !is_numeric($checkId))
            {
                CS_API::log('ERROR :: Online Booking failed at check creation step.');
                return Redirect::to('/disconnected');
            }

            Session::put('checkId',$checkId);

            //Bind the checkId to every onlineBookingReservation related to an item in the cart
            if ($cart !== null)
            {
                foreach ($cart as $cartItemId => $cartItem)
                {
                    if ($cartItem['type'] == 'heat')
                    {
                        $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                        $result = CS_API::bindCheckToOnlineReservation($onlineBookingsReservationId,$checkId);

                        if ($result === null)
                        {
                            CS_API::log("ERROR :: Online Booking failed to bind the checkId ($checkId) to the onlineBookingReservation ($onlineBookingsReservationId)! Customer was charged already, so proceeding as normal.");
                        }
                    }
                }
            }

            //Fetch the newly created check
            $check = CS_API::getCheck($checkId);
            if ($check === null || !property_exists($check,'checks'))
            {
                CS_API::log('ERROR :: Online Booking failed at the step it tried to fetch a check.');
                return Redirect::to('/disconnected');
            }

            Session::put('check',$check);

            //More data formatting
            $actualSubtotal = $check->checks[0]->checkSubtotal;
            $actualTax = $check->checks[0]->checkTax;
            $actualTotal = $check->checks[0]->checkTotal;

            $onlineBookingPaymentProcessorSettings = Session::get('onlineBookingPaymentProcessorSettings');
            Session::forget('onlineBookingPaymentProcessorSettings');

            $paymentInformation = array();

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
            Session::put('details',$details);

            $formattedTotalAmount = number_format($actualTotal,2,'.','');
            Session::put('formattedTotalAmount',$formattedTotalAmount);
            $onlineBookingPaymentProcessorSettings->amount = $formattedTotalAmount;
            Session::put('completePaymentProcessorSettings',$onlineBookingPaymentProcessorSettings);

            $onlineBookingPaymentProcessorSettings->returnUrl = URL::to('pay/redirect/return');
            $onlineBookingPaymentProcessorSettings->cancelUrl = URL::to('cart');
            $onlineBookingPaymentProcessorSettings->transactionId = $checkId;

            //Attempt the payment (just to get back redirection details)
            $result = CS_API::pay($onlineBookingPaymentProcessorSettings,$checkFormatted,$paymentInformation);
            if ($result === null)
            {
                CS_API::log('ERROR :: Online Booking failed at the make payment step for redirection.');
                return Redirect::to('/disconnected');
            }

            $isRedirect = (isset($result->type) && $result->type == "redirect");
            if ($isRedirect)
            {
                $redirectUrl = isset($result->redirectUrl) ? $result->redirectUrl : null;
                $redirectMethod = isset($result->redirectMethod) ? $result->redirectMethod : null;
                $redirectData = isset($result->redirectData) ? $result->redirectData : array();
                if (isset($redirectUrl) && isset($redirectMethod) && isset($redirectData))
                {
                    if ($redirectMethod == "POST")
                    {
                        return View::make('/payredirect',
                            array(
                                'images' => Images::getImageAssets(),
                                'strings' => $strings,
                                'redirectUrl' => $redirectUrl,
                                'redirectData' => $redirectData
                            )
                        );
                    }
                    else if ($redirectMethod == "GET")
                    {
                        return Redirect::away($redirectUrl);
                    }
                    else
                    {
                        CS_API::log("ERROR :: Online Booking failed at redirection step - unrecognized redirect method: $redirectMethod");
                        return Redirect::to('/disconnected');
                    }
                }
                else
                {
                    CS_API::log("ERROR :: Online Booking failed at redirection step - missing expected redirection data");
                    return Redirect::to('/disconnected');
                }
            }
            else
            {
                CS_API::log("ERROR :: Online Booking tried to use redirection on a payment processor type that didn't support it!");
                return Redirect::to('/disconnected');
            }
        }
        else
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_paymentDeclined']);
            return Redirect::to('/checkout')->withErrors($messages)->withInput(); //Go to checkout page
        }

    }

    public function payRedirectReturn()
    {

        $input = Input::all();
        $strings = Strings::getStrings();;

        if (Session::has('redirectionInProgress'))
        {
            Session::forget('redirectionInProgress');

            $paymentProcessorSettings = Session::get('completePaymentProcessorSettings');
            $result = CS_API::complete($paymentProcessorSettings, $input);

            $paymentWasSuccessful = (isset($result->type) && $result->type === "success");

            if ($paymentWasSuccessful) //If successful
            {
                //Remove all online booking reservations
                $cart = Session::get('cart');
                if ($cart !== null) {
                    foreach ($cart as $cartItemId => $cartItem) {
                        if ($cartItem['type'] == 'heat') {
                            $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                            $result = CS_API::makeOnlineReservationPermanent($onlineBookingsReservationId); //Mark the online booking reservation and permanent
                            if ($result === null) {
                                CS_API::log("ERROR :: Online Booking failed to make an online reservation permanent ($onlineBookingsReservationId)! Customer was charged already, so proceeding as normal.");
                            }
                        }
                    }
                }

                //Fetch needed values from the session
                $checkId = Session::get('checkId');
                $formattedTotalAmount = Session::get('formattedTotalAmount');
                $transactionId = isset($input['token']) ? $input['token'] : $checkId;
                $details = Session::get('details');
                $check = Session::get('check');
                $paymentInformation = array();

                //Insert a payment record
                $paymentInsertionSucceeded = CS_API::insertPaymentRecord($checkId, $formattedTotalAmount, $transactionId, $paymentProcessorSettings->name);
                if ($paymentInsertionSucceeded) {
                    //Finalize the check
                    $checkFinalizationSucceeded = CS_API::finalizeCheck($checkId, $details);
                    if (!$checkFinalizationSucceeded) {
                        CS_API::log("ERROR :: Online Booking failed to finalize the check! (Check ID: $checkId, Details: " . print_r($details, true) . " - Customer was charged already, so proceeding as normal.");
                    }
                } else {
                    CS_API::log("ERROR :: Online Booking failed to insert a payment record! (Check ID: $checkId, Total: $formattedTotalAmount, Transaction: $transactionId) Customer was charged already, so proceeding as normal, but skipping check finalization.");
                    $mostRecentAPICallResult = 'None!';

                    if (Session::has('callInfo'))
                    {
                        $callInfo = Session::get('callInfo');

                        //Prevent credit card information from being logged
                        if (isset($callInfo["params"]->card["number"])) { $callInfo["params"]->card["number"] = "XXXXXXXXXXXX".substr($callInfo["params"]->card["number"],-4); }
                        if (isset($callInfo["params"]->card["cvv"])) { $callInfo["params"]->card["cvv"] = "XXX"; }

                        $mostRecentAPICallResult = json_encode(var_export($callInfo,true));
                    }
                    CS_API::log('ERROR :: Online Booking user had an error: ' . $mostRecentAPICallResult);
                }

                //Direct to success page
                $successResults = array(
                    'check' => $check,
                    'checkId' => $checkId,
                    'paymentInformation' => $paymentInformation
                );
                return Redirect::to('/success')->with('successResults', $successResults);
            }
            else //If failure, redirect with error message
            {
                $checkId = Session::get('checkId');

                CS_API::log("INFO :: Online Booking - Payment was declined for check $checkId - voiding the check");

                $checkWasVoidedSuccessfully = CS_API::voidCheck($checkId);
                if (!$checkWasVoidedSuccessfully) {
                    CS_API::log("ERROR :: Online Booking - Check $checkId could not be voided!");
                }

                $messages = new Illuminate\Support\MessageBag;
                $messages->add('errors', $strings['str_paymentDeclined']);
                return Redirect::to('/cart')->withErrors($messages)->withInput(); //Go to checkout page
            }
        }
        else
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', $strings['str_paymentDeclined']);
            return Redirect::to('/checkout')->withErrors($messages)->withInput(); //Go to checkout page
        }
    }

    /**
     * Fetches the latest cart.
     * Checks for any expired items and automatically removes them.
     * If the state of the cart has changed, resets the checkId for the session and flags 'localCArtHasExpiredItem' to true.
     */
    private function getLatestCart()
    {
        $cart = Session::get('cart');

        //Get the current online reservations from Club Speed
        $currentOnlineReservations = CS_API::getOnlineReservations();
        if ($currentOnlineReservations === false || $currentOnlineReservations === null)
        {
            return null;
        }
        $listOfRemoteOnlineBookingReservationIds = array();
        foreach($currentOnlineReservations as $currentOnlineBookingReservation)
        {
            $listOfRemoteOnlineBookingReservationIds[] = $currentOnlineBookingReservation->onlineBookingReservationsId;
        }

        //See if any items in the local cart have been deleted from the Club Speed server
        $localCartHasExpiredItem = false;
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
            $cart = Session::get('cart'); //Update the cart to the latest version
            Session::put('localCartHasExpiredItem',true);
        }

        return $cart;
    }

    private function convertCartToVirtualCheckDetails($cart)
    {
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
        return $checkDetails;
    }

    private function convertCartToRealCheckDetails($cart)
    {
        //Package the data in the format expected by the API
        $checkDetails = array('checks' => array());
        $checkDetails['checks'][] = array(
            'customerId' => Session::get('authenticated'),
            'details' => array(),
            'broker' => Session::has('brokerName') ? Session::get('brokerName') : ''
        );

        //Format the current items in the cart for the Real Check API call
        foreach($cart as $cartItemIndex => $cartItem)
        {
            $newItem = array();
            if ($cartItem['type'] == 'heat')
            {
                $newItem['productId'] = $cartItem['data']->products[0]->productsId;
                $newItem['qty'] = $cartItem['quantity'];
                //$newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
            if ($cartItem['type'] == 'product')
            {
                $newItem['productId'] = $cartItem['itemId'];
                $newItem['qty'] = $cartItem['quantity'];
                //$newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                $checkDetails['checks'][0]['details'][] = $newItem;
            }
        }
        return $checkDetails;
    }

    private function generateListOfCountries($strings)
    {
        $countries = CS_API::getCountries();
        $countries = is_array($countries) ? $countries : null;
        $countriesFormatted = null;
        if ($countries != null)
        {
            $countriesFormatted = array();
            $countriesFormatted['ZZ'] = $strings['str_pleaseSelectAnOption'];
            foreach($countries as $country) {
                $countriesFormatted[$country->{'ISO_3166-1_Alpha_2'}] = $country->Name;
            }
        }
        return $countriesFormatted;
    }
} 