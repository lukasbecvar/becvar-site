<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin diagnostics component test
*/

class AdminDiagnosticTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    public function testDiagnostic(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to diagnostic page
        $this->client->request('GET', '/admin/diagnostic');

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | diagnostic');
        $this->assertSelectorTextContains('body', 'System diagnostics');
        $this->assertSelectorTextContains('body', 'Website diagnostics');
    }
}
