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
    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * UserFixtures constructor.
     *
     * @param SecurityUtil $securityUtil The security utility class for generating password hashes.
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager The EntityManager instance.
     */
    public function load(ObjectManager $manager): void
    {
        // generate testing users
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            
            // generate a random username
            $username = 'user_' . $i . '_' . uniqid();
            
            // set user properties
            $user->setUsername($username);
            $user->setPassword($this->securityUtil->genBcryptHash('testtest', 10));
            $user->setRole('User');
            $user->setIpAddress('127.0.0.1');
            $user->setToken(ByteString::fromRandom(32)->toString());
            $user->setRegistedTime(date('Y-m-d H:i:s'));
            $user->setLastLoginTime('not logged');
            $user->setProfilePic('profile_pic');
            $user->setVisitorId('1');

            // persist the entity
            $manager->persist($user);
        }

        // flush data to the database
        $manager->flush();
    }
}
