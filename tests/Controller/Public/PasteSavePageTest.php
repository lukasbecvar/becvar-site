<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PasteSavePageTest
 *
 * Test cases for the Paste Save component.
 *
 * @package App\Tests\Public
 */
class PasteSavePageTest extends WebTestCase
{
    private KernelBrowser $client;

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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
