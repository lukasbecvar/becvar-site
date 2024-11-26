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
        $this->assertSelectorExists('nav[id="navbar"]');
        $this->assertSelectorExists('a[class="nav-link"]');
        $this->assertSelectorExists('div[class="social-links"]');
        $this->assertSelectorExists('section[id="about"]');
        $this->assertSelectorExists('div[class="counts container"]');
        $this->assertSelectorExists('div[class="skills container"]');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
