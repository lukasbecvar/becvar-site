<?php

namespace App\Tests\Admin\Auth;

use App\Entity\User;
use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegisterTest
 *
 * Register component test.
 *
 * @package App\Tests\Admin\Auth
 */
class RegisterTest extends WebTestCase
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
     * Tear down after each test.
     */
    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake user data after each test.
     */
    private function removeFakeData(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);
        $fakeUser = $userRepository->findOneBy(['username' => 'testing_username']);

        // check if user exist
        if ($fakeUser) {
            $id = $fakeUser->getId();

            $entityManager->remove($fakeUser);
            $entityManager->flush();

            // reset auto-increment values for the users table
            $connection = $entityManager->getConnection();
            $connection->executeStatement("ALTER TABLE users AUTO_INCREMENT = " . ($id - 1));
        }
    }

    /**
     * Test if the register page is loaded when registration is allowed.
     */
    public function testRegisterAllowedLoaded(): void
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make get request to account settings admin component
        $this->client->request('GET', '/register');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin | Login');
        $this->assertSelectorTextContains('.form-title', 'Register admin account');
        $this->assertSelectorExists('form[name="register_form"]');
        $this->assertSelectorExists('input[name="register_form[username]"]');
        $this->assertSelectorExists('input[name="register_form[password]"]');
        $this->assertSelectorExists('input[name="register_form[re-password]"]');
        $this->assertSelectorExists('button:contains("Register")');
    }

    /**
     * Test if the register page redirects when registration is not allowed.
     */
    public function testRegisterNonAllowedLoaded(): void
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(false);
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make get request to account settings admin component
        $this->client->request('GET', '/register');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * Test if the register form handles empty submission correctly.
     */
    public function testRegisterEmptySubmit(): void
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // build post request
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => '',
                'password' => '',
                're-password' => ''
            ],
        ]);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a password again")', 'Please enter a password again');
    }

    /**
     * Test if the register form handles passwords that do not match correctly.
     */
    public function testRegisterNotMatchPasswordsSubmit(): void
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // build post request
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => 'testing_username',
                'password' => 'testing_password_1',
                're-password' => 'testing_password_2'
            ],
        ]);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Your passwords dont match');
    }
}
