<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin account settings test
*/

class AccountSettingsControllerTest extends WebTestCase
{
    private function createAuthManagerMock()
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    public function testAccountSettingsTable()
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings');

        // check response code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('h2', 'Account settings');
    }

    public function testAccountSettingsTableChangePicForm()
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/pic');

        // check response code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change profile image');
        $this->assertSelectorTextContains('button', 'Upload Image');
    }

    public function testAccountSettingsTableChangeUsernameForm()
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/username');

        // check response code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
    }

    public function testAccountSettingsTableChangePasswordForm()
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/password');

        // check response code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change password');
    }
}
