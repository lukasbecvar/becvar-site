<?php

namespace App\Tests\Admin;

use App\Service\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MessageInboxTest
 * 
 * Admin message inbox component test
 *
 * @package App\Tests\Admin
 */
class MessageInboxTest extends WebTestCase
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
     * @param string $role
     * @return object
     */
    private function createAuthManagerMock(string $role): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUserRole')->willReturn($role);

        return $authManagerMock;
    }

    /**
     * Test if the inbox page loads successfully for an admin user.
     */
    public function testInboxLoad(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('Admin'));

        // make post request to inbox page
        $this->client->request('GET', '/admin/inbox?page=1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | inbox');
    }

    /**
     * Test if the inbox page displays a permission error for a non-admin user.
     */
    public function testInboxLoadNoPermissions(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('User'));

        // make post request to inbox page
        $this->client->request('GET', '/admin/inbox?page=1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | inbox');
        $this->assertSelectorTextContains('h2', 'Sorry you dont have permission to this page');
    }
}
