<?php


class CheckoutTest extends TestCase
{
    //The checkout page should redirect to cart if there are no items in the cart or if the user isn't logged in
    public function testCheckoutRedirect()
    {
        $crawler = $this->client->request('GET', 'checkout');
        $this->assertRedirectedTo('cart');
    }

    public function testOnlineReservations()
    {
        //Ensure that we can query Club Speed for current reservations
        $result = CS_API::getOnlineReservations();
        $this->assertTrue($result !== null); //The call didn't fail
    }

    public function testVirtualCheck() //TODO: Evaluate this unit test based on availability of these specific products on any given CS server
    {
        $checkDetails = json_decode('{
                          "checks": [
                            {
                              "details": [
                                {
                                  "productId": 1,
                                  "qty": 1,
                                  "checkDetailId": 6
                                },
                                {
                                  "productId": 2,
                                  "qty": 2,
                                  "checkDetailId": 7
                                }
                              ]
                            }
                          ]
                        }');
        $result = CS_API::getVirtualCheck($checkDetails);
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(isset($result->checks)); //The call contains a check

        $firstCheck = $result->checks[0];
        //The check is in the format expected
        $this->assertTrue(isset($firstCheck->checkId));
        $this->assertTrue(isset($firstCheck->checkSubtotal));
        $this->assertTrue(isset($firstCheck->checkTax));
        $this->assertTrue(isset($firstCheck->checkTotal));
        $this->assertTrue(isset($firstCheck->details));
    }

    //CS_API::createCheck($checkDetails);

    //CS_API::getCheck($checkId);

    //CS_API::makePayment($onlineBookingPaymentProcessorSettings,$checkFormatted,$paymentInformation);

    //CS_API::makeOnlineReservationPermanent($onlineBookingsReservationId);
}