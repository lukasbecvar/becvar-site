<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProjectsControllerTest
 *
 * Test cases for projects page
 *
 * @package App\Tests\Public
 */
class ProjectsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test load projects page
     *
     * @return void
     */
    public function testLoadProjectsPage(): void
    {
        $this->client->request('GET', '/projects');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
