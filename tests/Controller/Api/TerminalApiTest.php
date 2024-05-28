<?php

namespace App\Tests\Controller\Api;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TerminalApiTest
 *
 * Admin terminal API test
 *
 * @package App\Tests\Api
 */
class TerminalApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Create a mock object for AuthManager.
     *
     * @param string $role The role of the user
     * @return object The mock object
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
     *
     * @return void
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('error this function is only for authentificated users!', $responseData['message']);
    }

    /**
     * Test executing terminal command with an empty command.
     *
     * @return void
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('command data is empty!', $responseData['message']);
    }

    /**
     * Test executing a valid terminal command.
     *
     * @return void
     */
    public function testTerminalExecValid(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'whoami',
        ]);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test executing a specific terminal command using a GET request.
     *
     * @return void
     */
    public function testTerminalExecGetPatch(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_path_1181517815187484',
        ]);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test executing a specific terminal command to get the hostname.
     *
     * @return void
     */
    public function testTerminalExecGetHostname(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_hostname_1181517815187484',
        ]);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
