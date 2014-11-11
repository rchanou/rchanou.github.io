<?php

//Due to the e-mailed nature of the reset token, there is no automated method of testing the actual reset
class ResetPasswordTest extends TestCase
{
    //The password reset request page should render and be passed its needed data
    public function testResetPasswordRequestView()
    {
        $crawler = $this->client->request('GET', 'resetpassword');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
    }

    //The password reset form page should render and be passed its needed data
    public function testResetPasswordFormView()
    {
        $crawler = $this->client->request('GET', 'resetpassword/form');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
    }
} 