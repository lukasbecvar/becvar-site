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
   // Instance for making requests
   private $client;

   protected function setUp(): void
   {
       parent::setUp();

       // Create client instance
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

       if ($fakeUser) {
           $id = $fakeUser->getId();

           $entityManager->remove($fakeUser);
           $entityManager->flush();

           // Reset auto-increment values for the users table
           $connection = $entityManager->getConnection();
           $connection->executeStatement("ALTER TABLE users AUTO_INCREMENT = " . ($id - 1));
       }
   }

   public function testRegisterNonAllowedLoaded(): void
   {
       // Create moc auth manager fake object
       $authManagerMock = $this->createMock(AuthManager::class);

       // Init fake testing value
       $authManagerMock->method('isRegisterPageAllowed')->willReturn(false);

       // Use fake auth manager instance
       $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

       // Make get request to account settings admin component
       $this->client->request('GET', '/register');

       // Check response code
       $this->assertSame(302, $this->client->getResponse()->getStatusCode());
   }

    public function testRegisterEmptyInputs(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

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

    public function testRegisterNotMatchPasswordsInputs(): void
    {
        // create moc auth manager fake object
        $authManagerMock = $this->createMock(AuthManager::class);

        // init fake testing value
        $authManagerMock->method('isRegisterPageAllowed')->willReturn(true);

        // use fake auth manager instance
        $this->client->getContainer()->set(AuthManager::class, $authManagerMock);

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
