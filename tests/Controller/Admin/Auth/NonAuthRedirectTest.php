<?php

namespace App\Tests\Controller\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Non auth redirect authenticator test 
    Test all admin routes in the default state when the user is not logged in (to test if it is correct check if user is logged in)
*/

class NonAuthRedirectTest extends WebTestCase
{
    public function testNonAuthRedirectAdminInit(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettings(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/account/settings');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsProfilePicsChange(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/account/settings/pic');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsUsernameChange(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/account/settings/username');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsPasswordChange(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/account/settings/password');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAdminChat(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/chat');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDashboard(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/dashboard');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminEmergencyShutdown(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/dashboard/emergency/shutdown');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminServiceRunner(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/dashboard/runner');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabase(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/database');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseTable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/database/table');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseEdit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/database/edit');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseAdd(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/database/add');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseDelete(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/database/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDiagnostics(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/diagnostic');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInbox(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/inbox');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInboxClose(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/inbox/close');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogs(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/logs');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsWhreIP(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/logs/whereip');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsDelete(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/logs/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsReadedAll(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/logs/readed/all');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminMediaBrowser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/media/browser');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTerminal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/terminal');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/todos');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminCompletedTodos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/todos/completed');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodosClose(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/todos/close');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitors(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/visitors');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsDelete(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/visitors/delete');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsBan(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/visitors/ban');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsUnban(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/visitors/unban');

        $this->assertResponseStatusCodeSame(302); 
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
}
