<?php

namespace App\Tests\Controller\Admin\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LogoutControllerTest
 *
 * Logout component test.
 *
 * @package App\Tests\Admin\Auth
 */
class LogoutControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the logout functionality
     *
     * @return void
     */
    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');

        // assert response
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseNotHasCookie('login-token-cookie');
    }
}
