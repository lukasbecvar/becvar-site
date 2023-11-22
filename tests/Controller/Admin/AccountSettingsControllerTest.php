<?php

namespace App\Tests\Controller\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Admin account settings test
*/

class AccountSettingsControllerTest extends WebTestCase
{
    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    public function testAccountSettingsTable(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('h2', 'Account settings');
    }

    public function testAccountSettingsTableChangePicForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/pic');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change profile image');
        $this->assertSelectorTextContains('button', 'Upload Image');
    }

    public function testAccountSettingsTableChangeUsernameForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/username');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
    }

    public function testAccountSettingsTableChangeUsernameEmptyForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $client->request('POST', '/admin/account/settings/username', [
            'username_change_form' => [
                'username' => ''
            ],
        ]);

        // check response content
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
    }

    public function testAccountSettingsTableChangeUsernameShortForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $client->request('POST', '/admin/account/settings/username', [
            'username_change_form' => [
                'username' => 'a'
            ],
        ]);

        // check response content
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
        $this->assertSelectorTextContains('li:contains("Your username should be at least 4 characters")', 'Your username should be at least 4 characters');
    }

    public function testAccountSettingsTableChangePasswordForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $client->request('GET', '/admin/account/settings/password');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorExists('form[name="password_change_form"]');
        $this->assertSelectorExists('input[name="password_change_form[password]"]');
        $this->assertSelectorExists('input[name="password_change_form[repassword]"]');
        $this->assertSelectorExists('button:contains("Change password")');
    }

    public function testAccountSettingsTableChangePasswordNotMatchForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => 'testing_password_1',
                'repassword' => 'testing_password_2'
            ],
        ]);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        // check response content
        $this->assertSelectorTextContains('body', 'Your passwords is not match!');
    }

    public function testAccountSettingsTableChangePasswordEmptyForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => '',
                'repassword' => ''
            ],
        ]);

        // check response content
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorTextContains('button', 'Change password');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a repassword")', 'Please enter a repassword');
    }

    public function testAccountSettingsTableChangePasswordShortForm(): void
    {
        $client = static::createClient();

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => 'a',
                'repassword' => 'a'
            ],
        ]);

        // check response content
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorTextContains('button', 'Change password');
        $this->assertSelectorTextContains('li:contains("Your password should be at least 8 characters")', 'Your password should be at least 8 characters');
        $this->assertSelectorTextContains('li:contains("Your password should be at least 8 characters")', 'Your password should be at least 8 characters');
    }
}
