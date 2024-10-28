<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VisitorManagerControllerTest
 *
 * Admin visitor manager component test
 *
 * @package App\Tests\Admin
 */
class VisitorManagerControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Create a mock object for AuthManager
     *
     * @param string $role The role of the user
     *
     * @return object The mock object
     */
    private function createAuthManagerMock(string $role = 'Admin'): object
    {
        // create a mock of AuthManager
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUserRole')->willReturn($role);

        return $authManagerMock;
    }

    /**
     * Test if the visitor manager page loads successfully
     *
     * @return void
     */
    public function testVisitorManager(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/admin/visitors?page=1');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | visitors');
        $this->assertSelectorTextContains('body', 'Online visitors');
        $this->assertSelectorTextContains('body', 'Banned visitors');
    }

    /**
     * Test load visitors export form
     *
     * @return void
     */
    public function testVisitorManagerExport(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/admin/visitors/download');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | visitors export');
        $this->assertSelectorTextContains('body', 'Download visitors data');
        $this->assertSelectorTextContains('body', 'Export data');
    }

    /**
     * Test load visitors metrics page
     *
     * @return void
     */
    public function testVisitorManagerMetrics(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/admin/visitors/metrics');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | visitors');
        $this->assertSelectorTextContains('body', 'Browser');
        $this->assertSelectorTextContains('body', 'Country');
        $this->assertSelectorTextContains('body', 'City');
    }
}
