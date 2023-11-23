<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Entity\User;
use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
    Register component test 
*/

class RegisterControllerTest extends WebTestCase
{
    // instance for making requests
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // create client instance
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

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

    public function testRegisterAllowedLoaded(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make get request to account settings admin component
        $this->client->request('GET', '/register');

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('title', 'Admin | Login');
        $this->assertSelectorTextContains('.form-title', 'Register admin account');
        $this->assertSelectorExists('form[name="register_form"]');
        $this->assertSelectorExists('input[name="register_form[username]"]');
        $this->assertSelectorExists('input[name="register_form[password]"]');
        $this->assertSelectorExists('input[name="register_form[re-password]"]');
        $this->assertSelectorExists('button:contains("Register")');
    }

    public function testRegisterNonAllowedLoaded(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(false);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // make get request to account settings admin component
        $this->client->request('GET', '/register');

        // check response code
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterEmptySubmit(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // build post request
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => '',
                'password' => '',
                're-password' => ''
            ],
        ]);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a password again")', 'Please enter a password again');
    }

    public function testRegisterNotMatchPasswordsSubmit(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

        // build post request
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => 'testing_username',
                'password' => 'testing_password_1',
                're-password' => 'testing_password_2'
            ],
        ]);

        // check response code
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // check response content
        $this->assertSelectorTextContains('body', 'Your passwords dont match');
    }
}
