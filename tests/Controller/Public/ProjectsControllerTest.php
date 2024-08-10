<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProjectsControllerTest
 *
 * Test cases for the Projects component
 *
 * @package App\Tests\Public
 */
class ProjectsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test accessing the Projects page
     *
     * @return void
     */
    public function testProjectsPage(): void
    {
        // make get request
        $this->client->request('GET', '/projects');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
