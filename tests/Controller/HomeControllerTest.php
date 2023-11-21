<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public final function testHomeResponseCode()
    {
        $client = static::createClient();

        $client->request('GET', '/home');

        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertEquals(200, $statusCode);
    }
}
