<?php

namespace App\Tests\Controller\Admin;

use App\Tests\CustomTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Class DatabaseBrowserControllerTest
 *
 * Test cases for database browser component
 *
 * @package App\Tests\Admin
 */
class DatabaseBrowserControllerTest extends CustomTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // simulate login
        $this->simulateLogin($this->client);
    }

    /**
     * Test load database browser page
     *
     * @return void
     */
    public function testLoadDatabaseBrowserTableList(): void
    {
        $this->client->request('GET', '/admin/database');

        // assert response
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.page-title', 'Select table');
        $this->assertSelectorExists('a[class="db-browser-select-link"]');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test load database table browser page
     *
     * @return void
     */
    public function testLoadDatabaseTableBrowser(): void
    {
        $this->client->request('GET', '/admin/database/table?table=users&page=1');

        // assert response
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('body', 'users');
        $this->assertSelectorNotExists('i[class="fa-arrow-left"]');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test load database table browser page with pagination
     *
     * @return void
     */
    public function testLoadAddRecordForm(): void
    {
        $this->client->request('GET', '/admin/database/add?table=users&page=1');

        // assert response
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.title', 'Add new: users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test load edit database record form
     *
     * @return void
     */
    public function testLoadEditRecordForm(): void
    {
        $this->client->request('GET', '/admin/database/edit?table=users&page=1&id=1');

        // assert response
        $this->assertSelectorTextContains('title', 'Admin | database');
        $this->assertSelectorTextContains('.title', 'Edit users, row: 1');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
