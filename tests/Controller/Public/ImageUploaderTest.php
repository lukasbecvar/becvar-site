<?php

namespace App\Tests\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Image uploader component test
*/

class ImageUploaderTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    public function testImageUploadPage()
    {
        // make get request
        $this->client->request('GET', '/image/uploader');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check form inputs
        $this->assertSelectorExists('p[class=form-title]');
        $this->assertSelectorExists('input[name=userfile]');
        $this->assertSelectorExists('input[name=submitUpload]');
    }
}
