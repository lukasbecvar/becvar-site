<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProjectsPageTest
 *
 * Test cases for the Projects component.
 *
 * @package App\Tests\Public
 */
class ProjectsPageTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test accessing the Projects page.
     *
     * @return void
     */
    public function testProjectsPage()
    {
        // make get request
        $this->client->request('GET', '/projects');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
