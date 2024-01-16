<?php

namespace App\Tests\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Non-auth redirect authenticator test.
 *
 * Test all admin routes in the default state when the user is not logged in
 *
 * @package App\Tests\Admin\Auth
 */
class NonAuthRedirectTest extends WebTestCase
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
        parent::setUp();
    }

    public function testNonAuthRedirectAdminInit(): void
    {
        $this->client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettings(): void
    {
        $this->client->request('GET', '/admin/account/settings');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsProfilePicsChange(): void
    {
        $this->client->request('GET', '/admin/account/settings/pic');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsUsernameChange(): void
    {
        $this->client->request('GET', '/admin/account/settings/username');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsPasswordChange(): void
    {
        $this->client->request('GET', '/admin/account/settings/password');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAdminChat(): void
    {
        $this->client->request('GET', '/admin/chat');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDashboard(): void
    {
        $this->client->request('GET', '/admin/dashboard');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminEmergencyShutdown(): void
    {
        $this->client->request('GET', '/admin/dashboard/emergency/shutdown');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminServiceRunner(): void
    {
        $this->client->request('GET', '/admin/dashboard/runner');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabase(): void
    {
        $this->client->request('GET', '/admin/database');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseTable(): void
    {
        $this->client->request('GET', '/admin/database/table');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseEdit(): void
    {
        $this->client->request('GET', '/admin/database/edit');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseAdd(): void
    {
        $this->client->request('GET', '/admin/database/add');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseDelete(): void
    {
        $this->client->request('GET', '/admin/database/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDiagnostics(): void
    {
        $this->client->request('GET', '/admin/diagnostic');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInbox(): void
    {
        $this->client->request('GET', '/admin/inbox');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInboxClose(): void
    {
        $this->client->request('GET', '/admin/inbox/close');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogs(): void
    {
        $this->client->request('GET', '/admin/logs');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsWhreIP(): void
    {
        $this->client->request('GET', '/admin/logs/whereip');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsDelete(): void
    {
        $this->client->request('GET', '/admin/logs/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsReadedAll(): void
    {
        $this->client->request('GET', '/admin/logs/readed/all');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminMediaBrowser(): void
    {
        $this->client->request('GET', '/admin/media/browser');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTerminal(): void
    {
        $this->client->request('GET', '/admin/terminal');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodos(): void
    {
        $this->client->request('GET', '/admin/todos');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminCompletedTodos(): void
    {
        $this->client->request('GET', '/admin/todos/completed');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodosClose(): void
    {
        $this->client->request('GET', '/admin/todos/close');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitors(): void
    {
        $this->client->request('GET', '/admin/visitors');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsDelete(): void
    {
        $this->client->request('GET', '/admin/visitors/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsBan(): void
    {
        $this->client->request('GET', '/admin/visitors/ban');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsUnban(): void
    {
        $this->client->request('GET', '/admin/visitors/unban');
        
        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }
}
