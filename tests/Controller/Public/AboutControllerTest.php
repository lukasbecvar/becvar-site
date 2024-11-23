<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AboutControllerTest
 *
 * Test cases for about page
 *
 * @package App\Tests\Public
 */
class AboutControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test load about page
     *
     * @return void
     */
    public function testLoadAboutPage(): void
    {
        $this->client->request('GET', '/about');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
