<?php

/**
 * Class Cart
 *
 * This static class maintains the Shopping Cart array in the session.
 * Each element in the cart array is an array of the following format:
 * array('itemId' => $itemId,'name' => $name,'quantity' => $quantity, 'type' => $type, 'startTime' => $startTime)
 * $cartItemId is unique and is the array index of each item in the cart
 * $itemId relates to a heatId
 *
 * TODO: Still being fleshed out:
 *
 * $itemToAdd = array('itemId' => $heatId (or $productId),'name' => $name,'quantity' => $quantity, 'type' => "heat", 'startTime' => $startTime, 'price' => $price,
    'onlineBookingsId' => $onlineBookingsId, 'data' => $productInfo[$heatId]);
 */
class Cart
{
    private function __construct() { }
    private static $initialized = false;

    //Creates an empty cart upon the start of the application
    private static function initialize()
    {
        if (self::$initialized) return;
        if (!Session::has('cart'))
        {
            Session::put('cart',array());
            Session::put('currentCartItemId',1);
        }
        self::$initialized = true;
    }

    //Returns the contents of the cart in the session
    public static function getCart()
    {
        self::initialize();
        return Session::get('cart');
    }

    //Adds the passed item to the cart
    public static function addToCart($cartItem)
    {
        self::initialize();
        $cart = Session::get('cart');

        $currentCartItemId = Session::get('currentCartItemId');

        $cart[$currentCartItemId] = $cartItem;
        Session::put('cart',$cart);
        $currentCartItemId = $currentCartItemId + 1;
        Session::put('currentCartItemId',$currentCartItemId);
        return true;
    }

    //Removes an item from the cart of the corresponding cartItemId
    public static function removeFromCart($cartItemId)
    {
        self::initialize();
        $cart = Session::get('cart');
        if(array_key_exists($cartItemId,$cart))
        {
            $name = $cart[$cartItemId]['name'];
            unset($cart[$cartItemId]);
            Session::put('cart',$cart);
            return $name;
        }
        else
        {
            return false;
        }
    }

    //Removes the last item added to the cart
    public static function removeMostRecentItemFromCart()
    {
        self::initialize();
        $currentCartItemId = Session::get('currentCartItemId');
        $itemIdToRemove = $currentCartItemId - 1;
        self::removeFromCart($itemIdToRemove);
    }

    //Returns the onlineBookingsReservationId of the specified cartItem
    public static function getOnlineBookingsReservationId($cartItemId)
    {
        self::initialize();
        $cart = Session::get('cart');
        if(array_key_exists($cartItemId,$cart))
        {
            return $cart[$cartItemId]['onlineBookingsReservationId'];
        }
        else
        {
            return false;
        }
    }

    //Records the onlineBookingReservationId for the item most recently added
    public static function insertOnlineBookingsReservationId($onlineBookingsReservationId)
    {
        self::initialize();
        $currentCartItemId = Session::get('currentCartItemId');
        $itemToUpdate = $currentCartItemId - 1;
        $cart = Session::get('cart');
        $cart[$itemToUpdate]['onlineBookingsReservationId'] = $onlineBookingsReservationId;
        Session::put('cart',$cart);
    }


}

