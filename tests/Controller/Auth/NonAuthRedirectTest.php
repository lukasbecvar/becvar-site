<?php

namespace App\Tests\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Non auth redirect authenticator test 
    Test all admin routes in the default state when the user is not logged in (to test if it is correct check if user is logged in)
*/

class NonAuthRedirectTest extends WebTestCase
{
    public function testNonAuthRedirectAdminInit()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettings()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/account/settings');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsProfilePicsChange()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/account/settings/pic');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsUsernameChange()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/account/settings/username');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAccountSettingsPasswordChange()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/account/settings/password');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testNonAuthRedirectAdminChat()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/chat');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDashboard()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/dashboard');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminEmergencyShutdown()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/dashboard/emergency/shutdown');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminServiceRunner()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/dashboard/runner');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabase()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/database');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseTable()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/database/table');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseEdit()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/database/edit');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseAdd()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/database/add');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDatabaseDelete()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/database/delete');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminDiagnostics()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/diagnostic');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInbox()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/inbox');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminInboxClose()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/inbox/close');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogs()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/logs');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsWhreIP()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/logs/whereip');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsDelete()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/logs/delete');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminLogsReadedAll()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/logs/readed/all');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminMediaBrowser()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/media/browser');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTerminal()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/terminal');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodos()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/todos');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminCompletedTodos()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/todos/completed');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminTodosClose()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/todos/close');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitors()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/visitors');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsDelete()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/visitors/delete');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsBan()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/visitors/ban');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }  

    public function testNonAuthRedirectAdminVisitorsUnban()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/admin/visitors/unban');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
}
