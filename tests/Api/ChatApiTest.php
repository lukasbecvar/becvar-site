<?php

namespace App\Tests\Api;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin chat api test
*/

class ChatTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(bool $logged): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn($logged);
        $authManagerMock->method('getUserToken')->willReturn('testing-user-token');

        return $authManagerMock;
    }

    public function testPostMessage(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(true));

        // build post data
        $postData = [
            'message' => 'Testing message: +ěščřžýáíé´=éíáýžřčš12345678ANFJNJNUJBZV',
        ];

        // make request
        $this->client->request('POST', '/api/chat/save/message', [], [], [], json_encode($postData));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response message
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('chat message saved', $responseData['message']);
    }

    public function testPostEmptyMessage(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(true));

        // build post data
        $postData = [];

        // make request
        $this->client->request('POST', '/api/chat/save/message', [], [], [], json_encode($postData));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(400);

        // check response message
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('chat message not saved', $responseData['message']);
    }

    public function testPostNonAuthMessage(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(false));

        // build post data
        $postData = [
            'message' => 'This is non authentificated message!'
        ];

        // make request
        $this->client->request('POST', '/api/chat/save/message', [], [], [], json_encode($postData));

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(401);

        // check response message
        $this->assertEquals('error to save message: only for authenticated users!', $responseData['message']);
    }

    public function testGetMessages(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(true));

        // make request
        $this->client->request('GET', '/api/chat/get/messages');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testNonAuthGetMessages(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock(false));

        // make request
        $this->client->request('GET', '/api/chat/get/messages');

        // get response data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // check response code
        $this->assertResponseStatusCodeSame(401);

        // check response message
        $this->assertEquals('error to get messages: only for authenticated users!', $responseData['message']);
    }
}
