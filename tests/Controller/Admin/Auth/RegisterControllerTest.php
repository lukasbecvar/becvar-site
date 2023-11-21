<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Register component of user authenticator test 
*/

class RegisterControllerTest extends WebTestCase
{
    public function testRegisterAllowedLoaded()
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
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | Login');
        $this->assertSelectorTextContains('.form-title', 'Register admin account');
        $this->assertSelectorTextContains('.input-button', 'Register');
    }

    public function testRegisterNonAllowedLoaded()
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
}
