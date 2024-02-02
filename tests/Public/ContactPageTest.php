<?php

namespace App\Tests\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ContactPageTest
 * 
 * Test cases for the Contact page.
 *
 * @package App\Tests\Public
 */
class ContactPageTest extends WebTestCase
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
     * Test accessing the Contact page.
     *
     * @return void
     */
    public function testContactPage()
    {
        // make get request
        $this->client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="contact_form"]');
        $this->assertSelectorExists('input[name="contact_form[name]"]');
        $this->assertSelectorExists('input[name="contact_form[email]"]');
        $this->assertSelectorExists('textarea[name="contact_form[message]"]');
        $this->assertSelectorExists('button:contains("Submit message")');
    }
}
