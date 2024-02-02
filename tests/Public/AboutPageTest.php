<?php

namespace App\Tests\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AboutPageTest
 * 
 * Test cases for the About page.
 *
 * @package App\Tests\Public
 */
class AboutPageTest extends WebTestCase
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
     * Test accessing the About page.
     *
     * @return void
     */
    public function testAboutPage()
    {
        // make get request
        $this->client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
