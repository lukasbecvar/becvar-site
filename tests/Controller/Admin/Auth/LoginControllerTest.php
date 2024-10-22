<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginControllerTest
 *
 * Login component test.
 *
 * @package App\Tests\Admin\Auth
 */
class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        // create client instance
        $this->client = static::createClient();

        // get entity manager
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // get user repository
        $userRepository = $entityManager->getRepository(\App\Entity\User::class);

        // get user
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
            $user->setRegistedTime(new DateTime());
            $user->setLastLoginTime(null);
            $user->setProfilePic('image');
            $user->setVisitorId(1);

            // persist and flush new user to the database
            $entityManager->persist($user);
            $entityManager->flush();
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake data from the database
     *
     * @return void
     */
    private function removeFakeData(): void
    {
        // get entity manager
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // get user repository
        $userRepository = $entityManager->getRepository(User::class);

        // get fake user
        $fakeUser = $userRepository->findOneBy(['username' => 'test_username']);

        // check if user exist
        if ($fakeUser) {
            $id = $fakeUser->getId();

            $entityManager->remove($fakeUser);
            $entityManager->flush();

            // reset auto-increment
            $connection = $entityManager->getConnection();
            $connection->executeStatement("ALTER TABLE users AUTO_INCREMENT = " . ($id - 1));
        }
    }

    /**
     * Test loading the login page
     *
     * @return void
     */
    public function testLoadLoginPage(): void
    {
        $this->client->request('GET', '/login');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Dashboard login');
        $this->assertSelectorTextContains('body', 'Remember me');
        $this->assertSelectorExists('form[name="login_form"]');
        $this->assertSelectorExists('input[name="login_form[username]"]');
        $this->assertSelectorExists('input[name="login_form[password]"]');
        $this->assertSelectorExists('input[name="login_form[remember]"]');
        $this->assertSelectorExists('button:contains("Sign in")');
    }

    /**
     * Test submitting the login form with empty fields
     *
     * @return void
     */
    public function testEmptyLoginSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = '';
        $form['login_form[password]'] = '';

        // submit form
        $this->client->submit($form);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
    }

    /**
     * Test submitting the login form with incorrect credentials
     *
     * @return void
     */
    public function testIncorrectLoginSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'password_1234_231622';

        // submit form
        $this->client->submit($form);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with an incorrect username
     *
     * @return void
     */
    public function testIncorrectUsernameLoginSubmit()
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'username_1234_848481';
        $form['login_form[password]'] = 'test_password';

        // submit form
        $this->client->submit($form);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with an incorrect password
     *
     * @return void
     */
    public function testIncorrectPassordLoginSubmit(): void
    {
        $crawler = $this->client->request('GET', '/login');

        // set form inputs
        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[username]'] = 'test_username';
        $form['login_form[password]'] = 'password_1234_231622';

        // submit form
        $this->client->submit($form);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with valid credentials
     *
     * @return void
     */
    public function testValidLoginSubmit(): void
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

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | dashboard');
    }
}
