<?php

require_once(app_path().'/includes/includes.php');

/**
 * Class CartController
 *
 * This controller deals with entry to the /cart page via various means.
 * It ultimately controls displaying the page, adding, and removing items from the cart.
 */
class CartController extends BaseController
{
    public function entry()
    {
        $settings = Settings::getSettings(true); //Force a refresh of website settings
        Session::put('settings',$settings);
        checkForCultureChange();

        $itemAddedToCart = null;
        $itemRemovedFromCart = null;
        $failureToAddToCart = null;
        $localCartHasExpiredItem = false;
        $virtualCheck = array();
        $virtualCheckDetails = array();

        $action = Input::get('action'); //Determine the intended action

        if (Session::has('authenticated')) //If the user is logged in
        {
            $productInfo = Session::get('productInfo'); //Grab the list of all possible heat products
            if ($productInfo == null) //If we hadn't fetched them yet, do so
            {
                $races = CS_API::getAvailableBookings();
                $this->recordProductInfo($races); //Remember every race and its details and store them in the session
                $productInfo = Session::get('productInfo');
            }
            $nonHeatProductInfo = Session::get('nonHeatProductInfo'); //Grab the list of all possible non-heat products
            if ($nonHeatProductInfo == null) //If we hadn't fetched them yet, do so
            {
                $nonHeatProducts = CS_API::getAllGiftCardProducts();
                $this->recordNonHeatProductInfo($nonHeatProducts); //Remember every race and its details and store them in the session
                $nonHeatProductInfo = Session::get('nonHeatProductInfo');
            }
            if ($action == "add" && Input::has('heatId') && Input::has('quantity')) //Adding heat item to cart
            {
                $heatId = Input::get('heatId');
                $quantity = Input::get('quantity');

                if (!array_key_exists($heatId, $productInfo)) //If the site hasn't cached this heat product yet (maybe it's far in the future)
                {
                    //Grab up to the next year worth of products and cache their data - restricted by booking availability window
                    $currentDateTime = new DateTime();
                    $today = $currentDateTime->format('Y-m-d') . 'T23:59:59';
                    $currentDateTime->add(new DateInterval('P1Y')); //Period 1 Year
                    $yearFromToday = $currentDateTime->format('Y-m-d') . 'T23:59:59';
                    $races = CS_API::getAvailableBookings($today,$yearFromToday);
                    $this->recordProductInfo($races); //Remember every race and its details and store them in the session
                    $productInfo = Session::get('productInfo');
                }
                if (array_key_exists($heatId,$productInfo)) //If the item exists in our handy list of available products and their information
                {
                    //Package all of the item's relevant data
                    $name = $productInfo[$heatId]->heatDescription;
                    $startTime = $productInfo[$heatId]->heatStartsAt;
                    $price = $productInfo[$heatId]->products[0]->price1;
                    $onlineBookingsId = $productInfo[$heatId]->products[0]->onlineBookingsId;

                    $itemToAdd = array('itemId' => $heatId,'name' => $name,'quantity' => $quantity, 'type' => "heat", 'startTime' => $startTime, 'price' => $price,
                        'onlineBookingsId' => $onlineBookingsId, 'data' => $productInfo[$heatId]);

                    $cartAddingWasSuccessfulLocally = Cart::addToCart($itemToAdd); //Add it to the cart locally

                    if ($cartAddingWasSuccessfulLocally)
                    {
                        Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.

                        $customersId = Session::get('authenticated');
                        $sessionId = Session::getId();
                        $onlineReservationResult = CS_API::createOnlineReservation($onlineBookingsId,$quantity,$sessionId,$customersId); //Then create an online reservation for the item

                        if ($onlineReservationResult === null)
                        {
                            return Redirect::to('/disconnected');
                        }
                        else if ($onlineReservationResult !== false && is_numeric($onlineReservationResult)) //If making the online reservation was successful
                        {
                            $itemAddedToCart = $name;
                            Cart::insertOnlineBookingsReservationId($onlineReservationResult);
                        }
                        else //If creating the online reservation failed, undo adding it to the local cart
                        {
                            $failureToAddToCart = true;
                            Cart::removeMostRecentItemFromCart();
                        }
                    }
                }
                else //If the item doesn't exist, report an error
                {
                    $failureToAddToCart = true;
                }
            }
            else if ($action == "add" && Input::has('productId') && Input::has('quantity'))
            {
                $productId = Input::get('productId');
                $quantity = Input::get('quantity');
                if ($quantity <= 0)
                {
                    $quantity = 1;
                }

                if (array_key_exists($productId,$nonHeatProductInfo)) //If the item exists in our handy list of available non-heat products and their information
                {
                    //Package all of the item's relevant data
                    $name = $nonHeatProductInfo[$productId]->description;
                    $price = $nonHeatProductInfo[$productId]->price1;

                    $itemToAdd = array('itemId' => $productId,'name' => $name, 'quantity' => $quantity, 'type' => "product", 'price' => $price);

                    $cartAddingWasSuccessfulLocally = Cart::addToCart($itemToAdd); //Add it to the cart locally

                    if ($cartAddingWasSuccessfulLocally)
                    {
                        $itemAddedToCart = $name;
                        Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.
                    }
                }
                else //If the item doesn't exist, report an error
                {
                    $failureToAddToCart = true;
                }

            }
            else if ($action == "delete") //Removing an item from cart
            {
                Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.

                $cartItemId = Input::get('item');
                $cart = Session::get('cart');

                if(isset($cart[$cartItemId]))
                {
                    if ($cart[$cartItemId]['type'] == 'heat')
                    {
                        $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                        CS_API::deleteOnlineReservation($onlineBookingsReservationId); //Remove the online booking
                    }

                    $name = Cart::removeFromCart($cartItemId); //Then remove it from the local cart

                    if ($name != false)
                    {
                        $itemRemovedFromCart = $name;
                    }
                }
            }

            $cart = Session::get('cart');
            $localCartHasExpiredItem = false;

            if ($cart !== null)
            {
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

                //See if any heat items in the local cart have been deleted from the Club Speed server
                foreach($cart as $cartItemId => $cartItem)
                {
                    if ($cartItem['type'] == 'heat')
                    {
                        if(!isset($cartItem['onlineBookingsReservationId']) || !in_array($cartItem['onlineBookingsReservationId'],$listOfRemoteOnlineBookingReservationIds)) //If the local item is out of sync
                        {
                            $localCartHasExpiredItem = true;
                            Cart::removeFromCart($cartItemId); //Then remove it from the local cart
                        }
                    }
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
            }

        }
        else //If the user wasn't logged in
        {
            if ($action == "add") //Remember their intent, and redirect them to the login screen
            {
                $intent = array('action' => $action,
                               'heatId' => Input::get('heatId'),
                               'productId' => Input::get('productId'),
                               'quantity' => Input::get('quantity'));

                Session::put('intent',$intent);
                return Redirect::to('/login');
            }
        }

        Session::forget('intent'); //Clear any past user intended actions

        $cart = Session::get('cart'); //Update the cart one last time

        $heatItems = 0;
        $nonHeatItems = 0;
        if ($cart != null)
        {
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
        }

        $settings = Session::get('settings');
        $locale = $settings['numberFormattingLocale'];
        $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::CURRENCY);
        $currency = $settings['currency'];

        return View::make('/cart',
            array(
                'images' => Images::getImageAssets(),
                'action' => $action,
                'cart' => $cart,
                'itemAddedToCart' => $itemAddedToCart,
                'itemRemovedFromCart' => $itemRemovedFromCart,
                'failureToAddToCart' => $failureToAddToCart,
                'localCartHasExpiredItem' => $localCartHasExpiredItem,
                'virtualCheck' => $virtualCheck,
                'virtualCheckDetails' => $virtualCheckDetails,
                'moneyFormatter' => $moneyFormatter,
                'currency' => $currency,
                'strings' => Strings::getStrings(),
                'settings' => $settings,
                'hasHeatItems' => $heatItems > 0 ? true : false,
                'hasNonHeatItems' => $nonHeatItems > 0 ? true : false
            )
        );
    }

    public function applyBrokerName()
    {
        $brokerName = Input::get('brokerName');
        $strings = Strings::getStrings();
        if ($brokerName != null)
        {
            Session::put('brokerName',$brokerName);
            Session::put('brokerNameSource','form'); //User input it themselves explicitly
        }
        else
        {
            Session::forget('brokerName');
            Session::forget('brokerNameSource');
        }
        return Redirect::to('cart')->with('message',$strings['str_affiliateCodeUpdated']);

    }

    /**
     * This function stores in the session every available heat product and its details.
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

    /**
     * This function stores in the session every available non-heat product and its details.
     * The cart will use this to render things like a product name, price, and so on.
     *
     * @param $products
     */
    public function recordNonHeatProductInfo($products)
    {
        $currentProductInfo = Session::get('nonHeatProductInfo');
        if ($currentProductInfo == null) { $currentProductInfo = array(); }
        $productInfo = $currentProductInfo;
        if ($products != null)
        {
            foreach($products as $currentProduct)
            {
                $productInfo[$currentProduct->productId] = $currentProduct;
            }
        }
        Session::put('nonHeatProductInfo',$productInfo);
    }
} 