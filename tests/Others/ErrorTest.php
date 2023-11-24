<?php

namespace App\Tests\Others;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ErrorTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    public function testErrorDefault()
    {
        // make get request
        $this->client->request('GET', '/error');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    public function testErrorBlockBanned()
    {
        // make get request
        $this->client->request('GET', '/error?code=banned');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    public function testErrorBlockMaintenance()
    {
        // make get request
        $this->client->request('GET', '/error?code=maintenance');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    public function testError400()
    {
        // make get request
        $this->client->request('GET', '/error?code=400');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Bad request');
        $this->assertSelectorTextContains('.error-page-msg', 'Request error');
    }

    public function testError401()
    {
        // make get request
        $this->client->request('GET', '/error?code=401');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Unauthorized');
        $this->assertSelectorTextContains('.error-page-msg', 'You do not have permission to access this page');
    }

    public function testError403()
    {
        // make get request
        $this->client->request('GET', '/error?code=403');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Forbidden');
        $this->assertSelectorTextContains('.error-page-msg', 'You do not have permission to access this page');
    }

    public function testError404()
    {
        // make get request
        $this->client->request('GET', '/error?code=404');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Page not found');
        $this->assertSelectorTextContains('.error-page-msg', 'Error this page was not found');
    }

    public function testError429()
    {
        // make get request
        $this->client->request('GET', '/error?code=429');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Too Many Requests');
        $this->assertSelectorTextContains('body', 'Too Many Requests');
        $this->assertSelectorTextContains('body', 'Please try to wait and try again later');
    }

    public function testError500()
    {
        // make get request
        $this->client->request('GET', '/error?code=500');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Error: Internal Server Error');
        $this->assertSelectorTextContains('.error-page-msg', 'The server encountered an unexpected condition that prevented it from fulfilling the reques');
    }
}
