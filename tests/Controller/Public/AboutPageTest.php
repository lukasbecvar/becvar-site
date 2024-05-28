<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
    private KernelBrowser $client;

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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
