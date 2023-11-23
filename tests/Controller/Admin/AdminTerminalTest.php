<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin terminal compnent test
*/

class AdminTerminalTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(string $role = 'Admin'): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        // check if simulated admin request
        if ($role == 'Admin') {
            $authManagerMock->method('isAdmin')->willReturn(true);
        }

        $authManagerMock->method('getUserRole')->willReturn($role);

        return $authManagerMock;
    }

    public function testAdminTerminalNoPermissions(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('User'));

        // make post request to admin init controller
        $this->client->request('GET', '/admin/terminal');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | terminal');

        $this->assertSelectorTextContains('h2', 'Sorry you dont have permission to this page');
    }

    public function testAdminTerminal(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to admin init controller
        $this->client->request('GET', '/admin/terminal');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | terminal');
        $this->assertSelectorTextContains('body', '$');
    }

    public function testTerminalExecNoPermissions(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('User'));

        // build post data
        $postData = [
            'command' => 'ls',
        ];

        // make request
        $this->client->request('POST', '/api/system/terminal', [], [], [], json_encode($postData));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(401);

        // check response message
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('error this function is only for authentificated users!', $responseData['message']);
    }

    public function testTerminalExecEmpty(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // build post data
        $postData = [
            'command' => '',
        ];

        // make request
        $this->client->request('POST', '/api/system/terminal', [], [], [], json_encode($postData));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(500);

        // check response message
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('command data is empty!', $responseData['message']);
    }

    public function testTerminalExecGet(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('GET', '/api/system/terminal');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(500);

        // check response message
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('POST request required!', $responseData['message']);
    }

    public function testTerminalExecValid(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'whoami',
        ]);

        // check response code
        $this->assertResponseStatusCodeSame(200);
    }

    public function testTerminalExecGetPatch(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_path_1181517815187484',
        ]);

        // check response code
        $this->assertResponseStatusCodeSame(200);
    }

    public function testTerminalExecGetHostname(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make request
        $this->client->request('POST', '/api/system/terminal', [
            'command' => 'get_current_hostname_1181517815187484',
        ]);

        // check response code
        $this->assertResponseStatusCodeSame(200);
    }
}
