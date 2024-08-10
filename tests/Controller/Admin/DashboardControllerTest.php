<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DashboardControllerTest
 *
 * Admin dashboard component test
 *
 * @package App\Tests\Admin
 */
class DashboardControllerTest extends WebTestCase
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
     * @return object The mock object
     */
    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUsername')->willReturn('phpunit-user');
        $authManagerMock->method('getUserRole')->willReturn('Admin');
        $authManagerMock->method('getUserProfilePic')->willReturn('image-code');

        return $authManagerMock;
    }

    /**
     * Test if the admin dashboard page loads successfully
     *
     * @return void
     */
    public function testAdminDashboard(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin dashboard controller
        $this->client->request('GET', '/admin/dashboard');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | dashboard');
        $this->assertSelectorExists('main[class="admin-page"]');
        $this->assertSelectorExists('img[alt="profile_picture"]');
        $this->assertSelectorExists('span[class="role-line"]');
        $this->assertSelectorTextContains('h3', 'phpunit-user');
        $this->assertSelectorTextContains('#wrarning-box', 'Warnings');
        $this->assertSelectorTextContains('body', 'Visitors info');
        $this->assertSelectorTextContains('.card-title', 'Logs');
        $this->assertSelectorTextContains('body', 'Messages');
        $this->assertSelectorTextContains('body', 'Visitors');
        $this->assertSelectorExists('a[class="logout-link"]');
        $this->assertSelectorExists('span[class="menu-text"]');
        $this->assertSelectorExists('div[class="sidebar"]');
    }
}
