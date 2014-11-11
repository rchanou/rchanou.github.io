<?php


class CartTest extends TestCase
{

    //The cart page should render and be passed its required data
    public function testCartView()
    {
        $crawler = $this->client->request('GET', 'cart');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
        $this->assertViewHas('action');
        $this->assertViewHas('cart');
        $this->assertViewHas('itemAddedToCart');
        $this->assertViewHas('itemRemovedFromCart');
        $this->assertViewHas('failureToAddToCart');
        $this->assertViewHas('localCartHasExpiredItem');
        $this->assertViewHas('virtualCheck');
        $this->assertViewHas('virtualCheckDetails');
    }

    public function testCartBookings()
    {
        //Ensure that we can query Club Speed for currently available bookings
        $result = CS_API::getAvailableBookings();
        $this->assertTrue($result !== null); //The call didn't fail
    }

    //TODO: Need ability to create heat on venue calendar, look up product that exists or create product if not exists - perhaps delete as well? Or just fetch existing product?
    //CS_API::createOnlineReservation($onlineBookingsId,$quantity,$sessionId,$customersId);

    //TODO: Need the above to be in place
    //CS_API::deleteOnlineReservation($onlineBookingsReservationId);

    public function testCartReservations()
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
} 