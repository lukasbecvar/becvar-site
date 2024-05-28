<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
    private KernelBrowser $client;

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

        // assert
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
