<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin database browser component test
*/

class DatabaseControllerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    private function createAuthManagerMock(string $role): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);
        $authManagerMock->method('getUserRole')->willReturn($role);

        return $authManagerMock;
    }

    public function testDatabaseBrowserList(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('Admin'));

        // make post request to admin init controller
        $this->client->request('GET', '/admin/database');

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.page-title', 'Select table');
        $this->assertSelectorExists('a[class="db-browser-select-link"]');
    }

    public function testDatabaseBrowserListNonPermissions(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('User'));

        // make post request to admin init controller
        $this->client->request('GET', '/admin/database');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.page-title', 'Sorry you dont have permission to this page');
        $this->assertSelectorNotExists('a[class="db-browser-select-link"]');
    }

    public function testDatabaseBrowserTableViewer(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('Admin'));

        // make post request to admin init controller
        $this->client->request('GET', '/admin/database/table?table=users&page=1');

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('body', 'NEW');
        $this->assertSelectorNotExists('i[class="fa-arrow-left"]');
    }

    public function testDatabaseBrowserNewRowAdder(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock('Admin'));

        // make post request to admin init controller
        $this->client->request('GET', '/admin/database/add?table=users&page=1');

        // check response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.title', 'Add new: users');
    }
}
