<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/*
    Admin todo manager component test
*/

class TodoManagerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    public function testTodoManager(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to todo manager controller
        $this->client->request('GET', '/admin/todos');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        
        // check response content
        $this->assertSelectorTextContains('title', 'Admin | todos');
        $this->assertSelectorTextContains('body', 'Completed');
        $this->assertSelectorExists('form[name="new_todo_form"]');
        $this->assertSelectorExists('textarea[name="new_todo_form[text]"]');
        $this->assertSelectorExists('button:contains("Add")');
    }

    public function testTodoManagerCompleted(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to todo manager controller
        $this->client->request('GET', '/admin/todos/completed');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        
        // check response content
        $this->assertSelectorTextContains('title', 'Admin | todos');
        $this->assertSelectorTextContains('body', 'Uncompleted');
        $this->assertSelectorNotExists('form[name="new_todo_form"]');
        $this->assertSelectorNotExists('textarea[name="new_todo_form[text]"]');
        $this->assertSelectorNotExists('button:contains("Add")');
    }
}
