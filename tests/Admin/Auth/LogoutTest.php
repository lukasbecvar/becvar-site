<?php

namespace App\Tests\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Logout component test.
 *
 * @package App\Tests\Admin\Auth
 */
class LogoutTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Instance for making requests.
     */
    private $client;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    /**
     * Test the logout functionality.
     */
    public function testLogout(): void
    {
        // make get request to logout
        $this->client->request('GET', '/logout');

        // check if logout redirected
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));

        // test response
        $this->assertResponseStatusCodeSame(302); 
        $this->assertResponseNotHasCookie('login-token-cookie');
    }
}
 