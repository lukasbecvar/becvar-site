<?php

namespace App\Tests\Controller\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Logout component test 
*/

class LogoutControllerTest extends WebTestCase
{
    public function testLogout(): void
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/logout');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));

        // check if login cookie unseted
        $this->assertResponseNotHasCookie('login-token-cookie');

        // Check response status code
        $this->assertResponseStatusCodeSame(302); 
    }
}
 