<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginTest
 *
 * Login component test.
 *
 * @package App\Tests\Admin\Auth
 */
class LoginTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser instance for making requests.
     */
    private $client;

    /**
     * Set up before each test.
     */
    protected function setUp(): void
    {
        // create client instance
        $this->client = static::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
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

        parent::setUp();
    }

    /**
     * Tear down after each test.
     */
    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake data from the database.
     */
    private function removeFakeData(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);
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
     * Test loading the login page.
     */
    public function testLoadLoginPage(): void
    {
        $this->client->request('GET', '/login');

        // assert
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
     * Test submitting the login form with empty fields.
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
    }

    /**
     * Test submitting the login form with incorrect credentials.
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with an incorrect username.
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with an incorrect password.
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Incorrect username or password');
    }

    /**
     * Test submitting the login form with valid credentials.
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

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | dashboard');
    }
}
