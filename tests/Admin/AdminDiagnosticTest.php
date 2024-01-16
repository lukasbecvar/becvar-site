<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Admin diagnostics component test
 *
 * @package App\Tests\Admin
 */
class AdminDiagnosticTest extends WebTestCase
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
    }

    /**
     * Create a mock object for AuthManager.
     *
     * @return object
     */
    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    /**
     * Test if the diagnostic page is loaded successfully.
     */
    public function testDiagnostic(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to diagnostic page
        $this->client->request('GET', '/admin/diagnostic');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | diagnostic');
        $this->assertSelectorTextContains('body', 'System diagnostics');
        $this->assertSelectorTextContains('body', 'Website diagnostics');
    }
}
