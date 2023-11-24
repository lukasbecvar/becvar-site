<?php

namespace App\Tests\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Logout component test 
*/

class LogoutTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    public function testLogout(): void
    {
        // make get request to logout
        $this->client->request('GET', '/logout');

        // check if logout redirected
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));

        // check if login cookie unseted
        $this->assertResponseNotHasCookie('login-token-cookie');

        // check response status code
        $this->assertResponseStatusCodeSame(302); 
    }
}
 