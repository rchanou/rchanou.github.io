<?php


class DisconnectedTest extends TestCase
{
    //The disconnected page should render and be passed its required data
    public function testDisconnectedView()
    {
        $crawler = $this->client->request('GET', 'disconnected');
        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertViewHas('images');
        $this->assertViewHas('errorInfo');
    }
} 