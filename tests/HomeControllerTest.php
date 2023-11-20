<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomeResponseCode()
    {
        $client = static::createClient();

        $client->request('GET', '/home');

        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertEquals(200, $statusCode);
    }
}
