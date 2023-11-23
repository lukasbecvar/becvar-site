<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin init controller test
*/

class AdminControllerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    public function testDashboardRedirect(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make post request to admin init controller
        $this->client->request('GET', '/admin');

        // check response
        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/dashboard'));
    }
}
