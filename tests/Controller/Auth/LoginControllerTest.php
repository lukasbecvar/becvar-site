<?php

namespace App\Tests\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Login authenticator test 
*/

final class LoginControllerTest extends WebTestCase
{
    public final function testLoginPageIsLoaded()
    {
        $client = static::createClient();

        // make get request to login page
        $client->request('GET', '/login');

        // check response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public final function testEmptyLoginFormSubmission()
    {
        $client = static::createClient();

        // make get request to login page
        $crawler = $client->request('GET', '/login'); 

        // set from data
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = '';
        $form['login_form[password]'] = '';

        // submit login form (post request)
        $client->submit($form);

        // check response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // check form validation
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
    }

    public final function testIncorrectLoginFormSubmission()
    {
        $client = static::createClient();

        // make get request to login page
        $crawler = $client->request('GET', '/login'); 

        // set from data
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'password_1234_231622';

        // submit from (post request)
        $client->submit($form);

        // check response
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // check incorect data message
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public final function testValidLoginFormSubmission()
    {
        $client = static::createClient();

        // make get request to login page
        $crawler = $client->request('GET', '/login'); 

        // set from data
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = $_ENV['VALID_USERNAME'];
        $form['login_form[password]'] = $_ENV['VALID_PASSWORD'];

        // submit form (post request)
        $client->submit($form);

        // follow redirect to admin dashboard
        $crawler = $client->followRedirect();

        // check if title is dashboard
        $this->assertSelectorTextContains('title', 'Admin | dashboard');
    }
}
