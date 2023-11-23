<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    public function testHomeResponseCode()
    {
        $this->client->request('GET', '/home');

        $statusCode = $this->client->getResponse()->getStatusCode();

        $this->assertEquals(200, $statusCode);
    }
}
