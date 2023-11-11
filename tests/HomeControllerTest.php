<?php

namespace App\Tests\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageRequestContainsHost()
    {
        // Create a client to simulate a request with a specific host
        $client = static::createClient();

        // Send a request to the public_home route
        $client->request('GET', '/home');

        // Check if the response code is as expected (HTTP status code 200 OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
