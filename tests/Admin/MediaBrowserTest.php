<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin media browser component test
 *
 * @package App\Tests\Admin
 */
class MediaBrowserTest extends WebTestCase
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
     * Test if the media browser page loads successfully.
     */
    public function testMediaBrowser(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to media browser controller
        $this->client->request('GET', '/admin/media/browser?page=1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | images');
    }
}
