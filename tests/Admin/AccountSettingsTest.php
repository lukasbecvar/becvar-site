<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Admin account settings test
 *
 * @package App\Tests\Admin
 */
class AccountSettingsTest extends WebTestCase
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
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    /**
     * Create a mock object for AuthManager.
     *
     * @return object
     */
    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    /**
     * Test if the account settings table page is loaded successfully.
     */
    public function testAccountSettingsTable(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $this->client->request('GET', '/admin/account/settings');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('h2', 'Account settings');
        $this->assertSelectorTextContains('body', 'profile-pic');
        $this->assertSelectorTextContains('body', 'username');
        $this->assertSelectorTextContains('body', 'password');
    }

    /**
     * Test if the account settings table page for changing the profile picture is loaded successfully.
     */
    public function testAccountSettingsTableChangePicForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $this->client->request('GET', '/admin/account/settings/pic');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change profile image');
        $this->assertSelectorTextContains('button', 'Upload Image');
    }

    /**
     * Test if the account settings table page for changing the username is loaded successfully.
     */
    public function testAccountSettingsTableChangeUsernameForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $this->client->request('GET', '/admin/account/settings/username');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
    }

    /**
     * Test if the account settings table page handles an empty username change form submission correctly.
     */
    public function testAccountSettingsTableChangeUsernameEmptyForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // build post request
        $this->client->request('POST', '/admin/account/settings/username', [
            'username_change_form' => [
                'username' => ''
            ],
        ]);

        // test response
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
    }

    /**
     * Test if the account settings table page handles a short username change form submission correctly.
     */
    public function testAccountSettingsTableChangeUsernameShortForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // build post request
        $this->client->request('POST', '/admin/account/settings/username', [
            'username_change_form' => [
                'username' => 'a'
            ],
        ]);

        // test response
        $this->assertSelectorTextContains('.form-title', 'Change username');
        $this->assertSelectorTextContains('button', 'Change username');
        $this->assertSelectorTextContains('li:contains("Your username should be at least 4 characters")', 'Your username should be at least 4 characters');
    }

    /**
     * Test if the account settings table page for changing the password is loaded successfully.
     */
    public function testAccountSettingsTableChangePasswordForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make get request to account settings admin component
        $this->client->request('GET', '/admin/account/settings/password');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | settings');
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorExists('form[name="password_change_form"]');
        $this->assertSelectorExists('input[name="password_change_form[password]"]');
        $this->assertSelectorExists('input[name="password_change_form[repassword]"]');
        $this->assertSelectorExists('button:contains("Change password")');
    }

    /**
     * Test if the account settings table page handles a password change form submission with non-matching passwords correctly.
     */
    public function testAccountSettingsTableChangePasswordNotMatchForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // build post request
        $this->client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => 'testing_password_1',
                'repassword' => 'testing_password_2'
            ],
        ]);

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Your passwords is not match!');
    }

    /**
     * Test if the account settings table page handles an empty password change form submission correctly.
     */
    public function testAccountSettingsTableChangePasswordEmptyForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $this->client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => '',
                'repassword' => ''
            ],
        ]);

        // test response
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorTextContains('button', 'Change password');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a repassword")', 'Please enter a repassword');
    }

    /**
     * Test if the account settings table page handles a short password change form submission correctly.
     */
    public function testAccountSettingsTableChangePasswordShortForm(): void
    {
        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        $this->client->request('POST', '/admin/account/settings/password', [
            'password_change_form' => [
                'password' => 'a',
                'repassword' => 'a'
            ],
        ]);

        // test response
        $this->assertSelectorTextContains('.form-title', 'Change password');
        $this->assertSelectorTextContains('button', 'Change password');
        $this->assertSelectorTextContains('li:contains("Your password should be at least 8 characters")', 'Your password should be at least 8 characters');
        $this->assertSelectorTextContains('li:contains("Your password should be at least 8 characters")', 'Your password should be at least 8 characters');
    }
}
