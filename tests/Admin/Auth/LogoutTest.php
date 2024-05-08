<?php

namespace App\Tests\Admin\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LogoutTest
 *
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
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the logout functionality.
     */
    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');

        // assert
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseNotHasCookie('login-token-cookie');
    }
}
