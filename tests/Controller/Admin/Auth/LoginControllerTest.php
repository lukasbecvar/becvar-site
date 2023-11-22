<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Login component test 
*/

class LoginControllerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();

        // initialize and create a fake user
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        
        // get user repository
        $userRepository = $entityManager->getRepository(\App\Entity\User::class);
        $existingUser = $userRepository->findOneBy(['username' => 'test_username']);
    
        // check if user exist
        if (!$existingUser) {

            // create a new User entity
            $user = new User();
            $user->setUsername('test_username');
            $user->setPassword(password_hash('test_password', PASSWORD_BCRYPT));
            $user->setRole('Owner');
            $user->setIpAddress('127.0.0.1');
            $user->setToken('zbjNNyuudM3HQGWe6xqWwjyncbtZB22D');
            $user->setRegistedTime('20.11.2023 14:13:06');
            $user->setLastLoginTime('22.11.2023 11:42:40');
            $user->setProfilePic('image');
            $user->setVisitorId('1');
    
            // persist and flush new user to the database
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    protected function tearDown(): void
    {
        // init entity manager
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // get user repository
        $userRepository = $entityManager->getRepository(\App\Entity\User::class);
        $fakeUser = $userRepository->findOneBy(['username' => 'test_username']);
    
        // check if fake user found
        if ($fakeUser) {

            // Remove the fake user
            $id = $fakeUser->getId();
            $entityManager->remove($fakeUser);
            $entityManager->flush();
    
            // Reset auto-increment value for the users table
            $connection = $entityManager->getConnection();
            $connection->executeStatement("ALTER TABLE users AUTO_INCREMENT = " . ($id - 1));
        }

        parent::tearDown();
    }

    public function testLoginPageLoad(): void
    {
        $this->client->request('GET', '/login');

        // check reponse
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check responsed components
        $this->assertSelectorTextContains('body', 'Dashboard login');
        $this->assertSelectorTextContains('body', 'Remember me');
        $this->assertSelectorExists('form[name="login_form"]');
        $this->assertSelectorExists('input[name="login_form[username]"]');
        $this->assertSelectorExists('input[name="login_form[password]"]');
        $this->assertSelectorExists('button:contains("Sign in")');
    }

    public function testEmptyLoginFormSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = '';
        $form['login_form[password]'] = '';

        // submit form
        $this->client->submit($form);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response message
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
    }

    public function testIncorrectLoginFormSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'password_1234_231622';

        // submit form
        $this->client->submit($form);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response message
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testIncorrectUsernameLoginFormSubmit()
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'test_password';

        // submit form
        $this->client->submit($form);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response message
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testIncorrectPassordLoginFormSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'test_username';
        $form['login_form[password]'] = 'password_1234_231622';

        // submit form
        $this->client->submit($form);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response message
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    public function testValidLoginFormSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'test_username';
        $form['login_form[password]'] = 'test_password';

        // submit form
        $this->client->submit($form);

        // check if login success
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('title', 'Admin | dashboard');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
