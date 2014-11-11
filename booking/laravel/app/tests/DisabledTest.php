<?php


class DisabledTest extends TestCase
{
    //The disabled page should render and be passed its required data
    public function testDisabledView()
    {
        $crawler = $this->client->request('GET', 'disabled');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
    }
} 