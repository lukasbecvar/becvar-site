<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/*
    Visitor (status) api test
*/

class VisitorApiTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    public function testVisitorStatusAPI(): void
    {
        // make post request to admin init controller
        $this->client->request('GET', '/api/visitor/update/activity');

        // check response code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
    }
}
