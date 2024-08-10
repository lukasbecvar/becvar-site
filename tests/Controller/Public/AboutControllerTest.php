<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AboutControllerTest
 *
 * Test cases for the About page
 *
 * @package App\Tests\Public
 */
class AboutControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test accessing the About page
     *
     * @return void
     */
    public function testAboutPage(): void
    {
        // make get request
        $this->client->request('GET', '/about');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
