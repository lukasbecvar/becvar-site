<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ChatTest
 *
 * Admin chat component test
 *
 * @package App\Tests\Admin
 */
class ChatTest extends WebTestCase
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
     * Create a mock object for AuthManager.
     *
     * @param bool $logged
     * @return object
     */
    private function createAuthManagerMock(bool $logged): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUserToken')->willReturn('testing-user-token');

        return $authManagerMock;
    }

    /**
     * Test if the admin chat page loads successfully.
     */
    public function testAdminChatLoad(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(true));

        // make post request to admin chat controller
        $this->client->request('GET', '/admin/chat');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | chat');
        $this->assertSelectorTextContains('h2', 'Chat');
        $this->assertSelectorExists('div[id="chat"]');
        $this->assertSelectorExists('input[id="message"]');
        $this->assertSelectorExists('button:contains("Send")');
        $this->assertSelectorExists('div[class="user-panel"]');
        $this->assertSelectorTextContains('body', 'User List (online)');
    }
}
