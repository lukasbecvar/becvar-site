<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 *
 * Test for doctrine entity repository class
 *
 * @package App\Tests\Repository
 */
class UserRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * Test get user by token
     *
     * @return void
     */
    public function testGetUserByToken(): void
    {
        /** @var \App\Repository\UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // get user by token
        $token = 'zHKrsWUjWZGJfi2dkpAEKrkkEpW2LHn2';
        $user = $userRepository->getUserByToken($token);

        // assert user instance and token match
        $this->assertInstanceOf(User::class, $user, 'Expected instance of User');
        $this->assertSame($token, $user->getToken(), 'The user token should match the input token');
    }

    /**
     * Test get all users with visitor ID
     *
     * @return void
     */
    public function testGetAllUsersWithVisitorId(): void
    {
        /** @var \App\Repository\UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // get all users with visitor IDs
        $users = $userRepository->getAllUsersWithVisitorId();

        // assert users array
        $this->assertIsArray($users, 'Expected result to be an array');

        // assert item result
        foreach ($users as $user) {
            $this->assertArrayHasKey('username', $user, 'Each user should have a username');
            $this->assertArrayHasKey('role', $user, 'Each user should have a role');
            $this->assertArrayHasKey('visitor_id', $user, 'Each user should have a visitor_id');
        }
    }
}
