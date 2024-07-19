<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IpInfoApiControllerTest
 *
 * IP Info API test
 *
 * @package App\Tests\Api
 */
class IpInfoApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the IP Info API.
     *
     * @return void
     */
    public function testIpInfoAPI(): void
    {
        // make post request to admin init controller
        $this->client->request('GET', '/api/ipinfo');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
