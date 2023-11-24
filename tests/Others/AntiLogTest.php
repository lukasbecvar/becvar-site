<?php

namespace App\Tests\Others;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AntiLogTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(bool $logged = true): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUsername')->willReturn('testing-user');

        return $authManagerMock;
    }

    public function testAntiLogSet(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // check response
        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/dashboard'));
    }

    public function testAntiLogNonAuth(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(false));

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(401);

        // check response message
        $this->assertEquals('error to set anti-log for non authentificated users!', $responseData['message']);
    }
}
