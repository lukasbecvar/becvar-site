<?php

namespace App\Tests\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test cases for the Paste Save component.
 *
 * @package App\Tests\Public
 */
class PasteSavePageTest extends WebTestCase
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
     * Test accessing the Paste Save page.
     *
     * @return void
     */
    public function testPastePage()
    {
        // make get request
        $this->client->request('GET', '/paste');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
