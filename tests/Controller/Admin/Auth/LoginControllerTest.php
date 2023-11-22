<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    
        // check if the user with the specified username already exists
        $userRepository = $entityManager->getRepository(\App\Entity\User::class);
        $existingUser = $userRepository->findOneBy(['username' => 'test_username']);
    
        // if the user not exist, create and persist fake data
        if (!$existingUser) {
            
            // init user entity
            $user = new User();

            // set main entity data
            $user->setUsername('test_username');
            $user->setPassword(password_hash('test_password', PASSWORD_BCRYPT));
    
            // set others data
            $user->setRole('Owner');
            $user->setIpAddress('127.0.0.1');
            $user->setToken('zbjNNyuudM3HQGWe6xqWwjyncbtZB22D');
            $user->setRegistedTime('20.11.2023 14:13:06');
            $user->setLastLoginTime('22.11.2023 11:42:40');
            $user->setProfilePic('image');
            $user->setVisitorId('1');
    
            // save data to database
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    public function testLoginPageIsLoaded()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEmptyLoginFormSubmission()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = '';
        $form['login_form[password]'] = '';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
    }

    public function testIncorrectLoginFormSubmission()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'password_1234_231622';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testIncorrectUsernameLoginFormSubmission()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'test_password';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testIncorrectPassordLoginFormSubmission()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'test_username';
        $form['login_form[password]'] = 'password_1234_231622';

        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testValidLoginFormSubmission()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'test_username';
        $form['login_form[password]'] = 'test_password';

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('title', 'Admin | dashboard');
    }
}
