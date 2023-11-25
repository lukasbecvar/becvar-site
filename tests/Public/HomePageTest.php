<?php

namespace App\Tests\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test cases for the Home component.
 *
 * @package App\Tests\Public
 */
class HomeTest extends WebTestCase
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
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    /**
     * Test accessing the Home page.
     *
     * @return void
     */
    public function testHomePage()
    {
        // make get request
        $this->client->request('GET', '/home');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test accessing the Home page using the default route.
     *
     * @return void
     */
    public function testHomeDefaultRote()
    {
        // make get request
        $this->client->request('GET', '/');

        // test response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
