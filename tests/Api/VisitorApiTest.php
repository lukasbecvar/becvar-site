<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VisitorApiTest
 * 
 * Visitor (status) API test
 *
 * @package App\Tests\Api
 */
class VisitorApiTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Instance for making requests.
     */
    private $client;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the Visitor Status API.
     */
    public function testVisitorStatusAPI(): void
    {
        // make post request to admin init controller
        $this->client->request('GET', '/api/visitor/update/activity');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK); 
    }
}
