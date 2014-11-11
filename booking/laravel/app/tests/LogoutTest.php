<?php


class LogoutTest extends TestCase
{
    //Logging out should result in the session being cleared and a redirect to step1
    public function testLogout()
    {
        $crawler = $this->client->request('GET', 'logout');
        $this->assertRedirectedTo('step1');
    }
} 