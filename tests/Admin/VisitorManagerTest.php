<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin visitor manager component test
 *
 * @package App\Tests\Admin
 */
class VisitorManagerTest extends WebTestCase
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
     * Create a mock object for AuthManager.
     *
     * @param string $role
     * @return object
     */
    private function createAuthManagerMock(string $role = 'Admin'): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUserRole')->willReturn($role);

        return $authManagerMock;
    }

    /**
     * Test if the visitor manager page loads successfully.
     */
    public function testVisitorManager(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/admin/visitors?page=1');

        // test response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | visitors');
        $this->assertSelectorTextContains('body', 'Online visitors');
        $this->assertSelectorTextContains('body', 'Banned visitors');
        $this->assertSelectorTextContains('body', 'Delete all');
        $this->assertSelectorTextContains('body', 'Unfiltered');
    }
}
