<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Register component test 
*/

class RegisterControllerTest extends WebTestCase
{
    public function testRegisterNonAllowedLoaded(): void
    {
        $client = static::createClient();
    
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);
    
        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(false);
    
        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $authManagerMock);
    
        // make get request to account settings admin component
        $client->request('GET', '/register');
    
        // check response code
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    public function testRegisterAllowedLoaded(): void
    {
        $client = static::createClient();

        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make get request to account settings admin component
        $client->request('GET', '/register');
        
        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | Login');
        $this->assertSelectorTextContains('body', 'Register admin account');
        $this->assertSelectorExists('form[name="register_form"]');
        $this->assertSelectorExists('input[name="register_form[username]"]');
        $this->assertSelectorExists('input[name="register_form[password]"]');
        $this->assertSelectorExists('input[name="register_form[re-password]"]');
        $this->assertSelectorExists('button:contains("Register")');
    }

    public function testRegisterEmptyInputs(): void
    {
        $client = static::createClient();

        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $authManagerMock);

        $client->request('POST', '/register', [
            'register_form' => [
                'username' => '',
                'password' => '',
                're-password' => ''
            ],
        ]);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a password again")', 'Please enter a password again');
    }

    public function testRegisterNotMatchPasswordsInputs(): void
    {
        $client = static::createClient();

        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $client->getContainer()->set(AuthManager::class, $authManagerMock);

        $client->request('POST', '/register', [
            'register_form' => [
                'username' => 'testing_username',
                'password' => 'testing_password_1',
                're-password' => 'testing_password_2',
            ],
        ]);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('body', 'Your passwords dont match');
    }
}
