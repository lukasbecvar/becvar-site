<?php

namespace App\Tests\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Logout authenticator test 
*/

final class LogoutControllerTest extends WebTestCase
{
    public final function testLogout()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/logout');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
}
