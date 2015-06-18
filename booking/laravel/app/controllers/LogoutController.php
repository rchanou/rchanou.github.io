<?php

/**
 * Class LogoutController
 *
 * This class handles the logic behind logging out a user.
 * Just clears the session and redirects to the front page.
 */
class LogoutController extends BaseController
{
    public function entry()
    {
        $cart = Session::get('cart'); //Remove all items from cart prior to logout
        if ($cart !== null)
        {
            foreach ($cart as $cartItemId => $cartItem)
            {
                if ($cart[$cartItemId]['type'] == 'heat')
                {
                    $onlineBookingsReservationId = Cart::getOnlineBookingsReservationId($cartItemId);
                    CS_API::deleteOnlineReservation($onlineBookingsReservationId); //Remove the online booking
                }
                Cart::removeFromCart($cartItemId); //Then remove it from the local cart
            }
        }

        Session::flush();
        Session::regenerate();
        return Redirect::to('/step1');
    }
} 