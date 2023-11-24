<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin chat component test
*/

class ChatTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(bool $logged): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUserToken')->willReturn('testing-user-token');

        return $authManagerMock;
    }

    public function testAdminChatLoad(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(true));

        // make post request to admin chat controller
        $this->client->request('GET', '/admin/chat');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | chat');
        $this->assertSelectorTextContains('h2', 'Chat');
        $this->assertSelectorExists('div[id="chat"]');
        $this->assertSelectorExists('input[id="message"]');
        $this->assertSelectorExists('button:contains("Send")');
        $this->assertSelectorExists('div[class="user-panel"]');
        $this->assertSelectorTextContains('body', 'User List (online)');
    }
}
