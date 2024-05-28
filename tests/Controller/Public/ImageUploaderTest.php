<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ImageUploaderTest
 *
 * Test cases for the Image Uploader component.
 *
 * @package App\Tests\Public
 */
class ImageUploaderTest extends WebTestCase
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
     * Test accessing the Image Uploader page.
     *
     * This test checks if the page loads successfully and if the expected form elements are present.
     *
     * @return void
     */
    public function testImageUploadPage()
    {
        // make get request
        $this->client->request('GET', '/image/uploader');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('p[class=form-title]');
        $this->assertSelectorExists('input[name=userfile]');
        $this->assertSelectorExists('input[name=submitUpload]');
    }
}
