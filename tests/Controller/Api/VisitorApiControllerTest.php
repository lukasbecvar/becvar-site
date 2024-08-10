<?php

namespace App\Tests\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VisitorApiControllerTest
 *
 * Visitor (status) API test
 *
 * @package App\Tests\Api
 */
class VisitorApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the Visitor Status API
     *
     * @return void
     */
    public function testVisitorStatusAPI(): void
    {
        // make post request to admin init controller
        $this->client->request('GET', '/api/visitor/update/activity');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
