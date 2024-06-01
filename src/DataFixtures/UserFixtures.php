<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\ByteString;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class UserFixtures
 *
 * Fixture class to generate testing users.
 *
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager The EntityManager instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // add test user
        $testUser = new User();
        $testUser->setUsername('test')
            ->setPassword($this->securityUtil->genBcryptHash('test', 10))
            ->setRole('Owner')
            ->setIpAddress('127.0.0.1')
            ->setToken(ByteString::fromRandom(32)->toString())
            ->setRegistedTime(date('Y-m-d H:i:s'))
            ->setLastLoginTime('not logged')
            ->setProfilePic('non-pic')
            ->setVisitorId('1');

        // persist the entity
        $manager->persist($testUser);

        // generate testing users
        for ($i = 2; $i < 50; $i++) {
            $user = new User();

            // generate a random username
            $username = 'user_' . $i . '_' . uniqid();

            // set user properties
            $user->setUsername($username)
                ->setPassword($this->securityUtil->genBcryptHash('testtest', 10))
                ->setRole('User')
                ->setIpAddress('127.0.0.1')
                ->setToken(ByteString::fromRandom(32)->toString())
                ->setRegistedTime(date('Y-m-d H:i:s'))
                ->setLastLoginTime('not logged')
                ->setProfilePic('profile_pic')
                ->setVisitorId(strval($i));

            // persist the entity
            $manager->persist($user);
        }

        // flush data to the database
        $manager->flush();
    }
}
