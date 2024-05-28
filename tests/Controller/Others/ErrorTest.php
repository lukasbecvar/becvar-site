<?php

namespace App\Tests\Controller\Others;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ErrorTest
 *
 * Test cases for handling different error scenarios.
 *
 * @package App\Tests\Others
 */
class ErrorTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the default error page.
     *
     * @return void
     */
    public function testErrorDefault()
    {
        // make get request
        $this->client->request('GET', '/error');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    /**
     * Test error block for banned users.
     *
     * @return void
     */
    public function testErrorBlockBanned()
    {
        // make get request
        $this->client->request('GET', '/error?code=banned');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    /**
     * Test error block for maintenance mode.
     *
     * @return void
     */
    public function testErrorBlockMaintenance()
    {
        // make get request
        $this->client->request('GET', '/error?code=maintenance');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: unknown');
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    /**
     * Test error for Bad Request (400).
     *
     * @return void
     */
    public function testError400()
    {
        // make get request
        $this->client->request('GET', '/error?code=400');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Bad request');
        $this->assertSelectorTextContains('.error-page-msg', 'Request error');
    }

    /**
     * Test error for Unauthorized (401).
     *
     * @return void
     */
    public function testError401()
    {
        // make get request
        $this->client->request('GET', '/error?code=401');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Unauthorized');
        $this->assertSelectorTextContains('.error-page-msg', 'You do not have permission to access this page');
    }

    /**
     * Test error for Forbidden (403).
     *
     * @return void
     */
    public function testError403()
    {
        // make get request
        $this->client->request('GET', '/error?code=403');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Forbidden');
        $this->assertSelectorTextContains('.error-page-msg', 'You do not have permission to access this page');
    }

    /**
     * Test error for Page Not Found (404).
     *
     * @return void
     */
    public function testError404()
    {
        // make get request
        $this->client->request('GET', '/error?code=404');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Page not found');
        $this->assertSelectorTextContains('.error-page-msg', 'Error this page was not found');
    }

    /**
     * Test error for Too Many Requests (429).
     *
     * @return void
     */
    public function testError429()
    {
        // make get request
        $this->client->request('GET', '/error?code=429');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Too Many Requests');
        $this->assertSelectorTextContains('body', 'Too Many Requests');
        $this->assertSelectorTextContains('body', 'Please try to wait and try again later');
    }

    /**
     * Test error for Internal Server Error (500).
     *
     * @return void
     */
    public function testError500()
    {
        // make get request
        $this->client->request('GET', '/error?code=500');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Error: Internal Server Error');
        $this->assertSelectorTextContains('.error-page-msg', 'The server encountered an unexpected condition that prevented it from fulfilling the reques');
    }
}
