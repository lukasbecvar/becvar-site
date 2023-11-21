<?php

namespace App\Tests\Controller\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Logout authenticator test 
*/

class LogoutControllerTest extends WebTestCase
{
    public function testLogout()
    {
        $client = static::createClient();

        // make get request to logout
        $client->request('GET', '/logout');

        // check if logout redirected
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }
}
