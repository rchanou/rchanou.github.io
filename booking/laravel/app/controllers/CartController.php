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
        $itemAddedToCart = null;
        $itemRemovedFromCart = null;
        $failureToAddToCart = null;
        $localCartHasExpiredItem = false;
        $virtualCheck = array();
        $virtualCheckDetails = array();

        $action = Input::get('action'); //Determine the intended action

        if (Session::has('authenticated')) //If the user is logged in
        {
            $productInfo = Session::get('productInfo'); //Grab the list of all possible products

            if ($action == "add" && Input::has('heatId') && Input::has('quantity')) //Adding item to cart
            {
                $heatId = Input::get('heatId');
                $quantity = Input::get('quantity');

                if (array_key_exists($heatId,$productInfo)) //If the item exists
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
            }
            else if ($action == "delete") //Removing an item from cart
            {
                Session::forget('checkId'); //Drop any checks we've used; we'll need to make a new one.

                $cartItemId = Input::get('item');

                $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                CS_API::deleteOnlineReservation($onlineBookingsReservationId); //Remove the online booking
                $name = Cart::removeFromCart($cartItemId); //Then remove it from the local cart

                if ($name != false)
                {
                    $itemRemovedFromCart = $name;
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

                //See if any items in the local cart have been deleted from the Club Speed server
                foreach($cart as $cartItemId => $cartItem)
                {
                    if(!in_array($cartItem['onlineBookingsReservationId'],$listOfRemoteOnlineBookingReservationIds)) //If the local item is out of sync
                    {
                        $localCartHasExpiredItem = true;
                        Cart::removeFromCart($cartItemId); //Then remove it from the local cart
                    }
                }

                //Format the current items in the cart for the Virtual Check API call
                $checkDetails = array('checks' => array());
                $checkDetails['checks'][0] = array('details' => array());
                foreach($cart as $cartItemIndex => $cartItem)
                {
                    $newItem = array();
                    $newItem['productId'] = $cartItem['data']->products[0]->productsId;
                    $newItem['qty'] = $cartItem['quantity'];
                    $newItem['checkDetailId'] = $cartItemIndex; //Used to map the cart to the resulting check details
                    $checkDetails['checks'][0]['details'][] = $newItem;
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

        $cart = Session::get('cart'); //Update the cart one last time

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
                'virtualCheckDetails' => $virtualCheckDetails
            )
        );
    }
} 