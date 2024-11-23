<?php

namespace App\Tests;

use DateTime;
use App\Entity\User;
use App\Manager\AuthManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CustomTestCase
 *
 * Custom test case class
 *
 * @package App\Tests
 */
class CustomTestCase extends WebTestCase
{
    /**
     * Simulate a user login
     *
     * @param KernelBrowser $client The KernelBrowser instance
     *
     * @return void
     */
    public function simulateLogin(KernelBrowser $client): void
    {
        // create a mock user
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

        // create a mock of AuthManager
        $authManager = $this->createMock(AuthManager::class);

        // configure the mock to return true for isUserLogedin
        $authManager->method('isUserLogedin')->willReturn(true);

        // configure the mock to return the mock user for getLoggedUserRepository
        $authManager->method('getUserRepository')->willReturn($user);

        // replace the actual AuthManager service with the mock
        $client->getContainer()->set('App\Manager\AuthManager', $authManager);
    }

    /**
     * Allow registration for the current test
     *
     * @param KernelBrowser $client The KernelBrowser instance
     * @param bool $allow The allow registration flag
     *
     * @return void
     */
    public function allowRegistration(KernelBrowser $client, bool $allow = true): void
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isRegisterPageAllowed')->willReturn($allow);
        $client->getContainer()->set(AuthManager::class, $authManagerMock);
    }
}
