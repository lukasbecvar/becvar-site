<?php

namespace App\Tests\Api;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Admin terminal API test
 *
 * @package App\Tests\Api
 */
class TerminalApiTest extends WebTestCase
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
     * @param string $role
     * @return object
     */
    private function createAuthManagerMock(string $role = 'Admin'): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUserRole')->willReturn($role);

        // check if simulated admin request
        if ($role == 'Admin') {
            $authManagerMock->method('isAdmin')->willReturn(true);
        }

        return $authManagerMock;
    }

    /**
     * Test executing terminal command with no permissions.
     */
    public function testTerminalExecNoPermissions(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('User'));

        // build post request
        $this->client->request('POST', '/api/system/terminal', [], [], [], json_encode([
            'command' => 'ls',
        ]));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(401);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('error this function is only for authentificated users!', $responseData['message']);
    }

    /**
     * Test executing terminal command with an empty command.
     */
    public function testTerminalExecEmpty(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // build post request
        $this->client->request('POST', '/api/system/terminal', [], [], [], json_encode([
            'command' => '',
        ]));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('command data is empty!', $responseData['message']);
    }

    /**
     * Test executing terminal command with a GET request.
     */
    public function testTerminalExecGet(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('GET', '/api/system/terminal');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('POST request required!', $responseData['message']);
    }

    /**
     * Test executing a valid terminal command.
     */
    public function testTerminalExecValid(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'whoami',
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * Test executing a specific terminal command using a GET request.
     */
    public function testTerminalExecGetPatch(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_path_1181517815187484',
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * Test executing a specific terminal command to get the hostname.
     */
    public function testTerminalExecGetHostname(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_hostname_1181517815187484',
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
