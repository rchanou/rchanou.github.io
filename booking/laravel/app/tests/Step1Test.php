<?php


class Step1Test extends TestCase
{
    //The root URL should redirect to step1
    public function testIndexRedirect()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertRedirectedTo('step1');
    }

    //The step1 page should render and be passed its needed data
    public function testStep1View()
    {
        $crawler = $this->client->request('GET', 'step1');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
        $this->assertViewHas('strings');
        $this->assertViewHas('heatTypes');
        $this->assertViewHas('maxRacers');
    }

    //Step1's API calls should all function
    public function testStep1ViewAPICalls()
    {
        //Ensure that we can fetch general settings from Club Speed
        $result = CS_API::getSettings();
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(isset($result->settings)); //The call contained a settings object
        $this->assertTrue(count($result->settings) > 0); //The call contained some actual settings

        //Ensure that we can fetch Booking settings from Club Speed
        $result = CS_API::getBookingSettings();
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(count($result) > 0); //The call contained some actual settings
        $this->assertTrue(isset($result['registrationEnabled'])); //The call contained a critical Booking setting
        $this->assertTrue(isset($result['onlineBookingPaymentProcessorSettings'])); //The call contained a critical financial setting

        //Ensure that we can initially populate the dropdown with available bookings server-side
        $result = CS_API::getAvailableBookingsForDropdown();
        $this->assertTrue($result !== null); //The call didn't fail
        $this->assertTrue(count($result) > 0); //The call contained at least one item for the dropdown menu

        //Ensure that the first item in the result set is our expected default for the 'All' option in the dropdown menu
        $defaultResult = $result[0];
        $this->assertTrue(isset($defaultResult['heatTypeId']));
        $this->assertEquals(-1,$defaultResult['heatTypeId']);
        $this->assertTrue(isset($defaultResult['name']));
        $this->assertEquals('All',$defaultResult['name']);
        $this->assertTrue(isset($defaultResult['heatSpotsAvailableOnline']));
        $this->assertEquals('999',$defaultResult['heatSpotsAvailableOnline']);
    }

} 