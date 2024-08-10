<?php

namespace App\Tests\Controller\Others;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AntilogControllerTest
 *
 * Test cases for the AntiLog functionality
 *
 * @package App\Tests\Others
 */
class AntilogControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * Set up before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Create a mock instance of the AuthManager
     *
     * @param bool $logged Whether the user is logged in or not
     *
     * @return object The mock AuthManager instance
     */
    private function createAuthManagerMock(bool $logged = true): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUsername')->willReturn('testing-user');

        return $authManagerMock;
    }

    /**
     * Test setting AntiLog for an authenticated user
     *
     * @return void
     */
    public function testAntiLogSet(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/dashboard'));
    }

    /**
     * Test setting AntiLog for a non-authenticated user
     *
     * @return void
     */
    public function testAntiLogNonAuth(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(false));

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('error to set anti-log for non authentificated users!', $responseData['message']);
    }
}
