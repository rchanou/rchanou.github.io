<?php


class SuccessTest extends TestCase
{
    //If the user has not just completed a purchase, they should be redirected to step1
    public function testIndexRedirect()
    {
        $crawler = $this->client->request('GET', 'success');
        $this->assertRedirectedTo('step1');
    }

    //The success page should render and be passed its needed data if a user just completed a purchase
    public function testSuccessView()
    {
        $this->session(array('authenticated' => true, 'successResults' => array('check' => 1, 'checkId' => 1, 'paymentInformation' => 1)));
        $crawler = $this->client->request('GET', 'success');
        $this->assertTrue($this->client->getResponse()->isOk());
    }
} 