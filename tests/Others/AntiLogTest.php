<?php

namespace App\Tests\Others;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test cases for the AntiLog functionality.
 *
 * @package App\Tests\Others
 */
class AntiLogTest extends WebTestCase
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
     * Create a mock instance of the AuthManager.
     *
     * @param bool $logged Whether the user is logged in or not.
     * @return object The mock AuthManager instance.
     */
    private function createAuthManagerMock(bool $logged = true): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUsername')->willReturn('testing-user');

        return $authManagerMock;
    }

    /**
     * Test setting AntiLog for an authenticated user.
     */
    public function testAntiLogSet(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // test response
        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/dashboard'));
    }

    /**
     * Test setting AntiLog for a non-authenticated user.
     */
    public function testAntiLogNonAuth(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(false));

        // make post request to admin init controller
        $this->client->request('GET', '/antilog/5369362536');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // test response
        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals('error to set anti-log for non authentificated users!', $responseData['message']);
    }
}
