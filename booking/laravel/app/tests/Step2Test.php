<?php


class Step2Test extends TestCase
{

    //If the user has not made a search, they should be redirected to step1
    public function testStep2RequireSearch()
    {
        $crawler = $this->client->request('GET', 'step2');
        $this->assertRedirectedTo('step1');
    }

    //The step2 page should render and be passed its needed data if the user made a search
    public function testStep2View()
    {
        $dateFormat = Config::get('config.dateFormat');
        $currentDateTime = new DateTime();
        $today = $currentDateTime->format($dateFormat);
        $this->session(array('lastSearch' => array('start' => $today,'numberOfParticipants' => 1, 'heatType' => 1))); //Manually setting a user search
        $crawler = $this->client->request('GET', 'step2');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
        $this->assertViewHas('races');
        $this->assertViewHas('start');
        $this->assertViewHas('previousDay');
        $this->assertViewHas('previousDayDisplay');
        $this->assertViewHas('nextDayDisplay');
        $this->assertViewHas('nextDay');
        $this->assertViewHas('heatType');
        $this->assertViewHas('numberOfParticipants');
        $this->assertViewHas('authenticated');
        $this->assertViewHas('loginToAccountErrors');
        $this->assertViewHas('createAccountErrors');
        $this->assertViewHas('settings');
        $this->assertViewHas('strings');
    }

    //Step2's API calls should all function
    public function testStep2ViewAPICalls()
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
        $result = CS_API::getAvailableBookings();
        $this->assertTrue($result !== null); //The call didn't fail

    }

} 