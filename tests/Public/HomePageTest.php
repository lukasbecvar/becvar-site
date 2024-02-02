<?php

namespace App\Tests\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomeTest
 * 
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
        $this->client = static::createClient();
        parent::setUp();
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

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
