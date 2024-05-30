<?php

namespace App\Tests\Controller\Admin\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class NonAuthRedirectTest
 *
 * Non-auth redirect authenticator test.
 * Test all admin routes in the default state when the user is not logged in
 *
 * @package App\Tests\Admin\Auth
 */
class NonAuthRedirectTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Admin routes list
     *
     * @return array<array<string>>
     */
    public function provideAdminUrls(): array
    {
        return [
            ['/admin'],
            ['/admin/account/settings'],
            ['/admin/account/settings/pic'],
            ['/admin/account/settings/username'],
            ['/admin/account/settings/password'],
            ['/admin/chat'],
            ['/admin/dashboard'],
            ['/admin/dashboard/emergency/shutdown'],
            ['/admin/dashboard/runner'],
            ['/admin/database'],
            ['/admin/database/table'],
            ['/admin/database/edit'],
            ['/admin/database/add'],
            ['/admin/database/delete'],
            ['/admin/diagnostic'],
            ['/admin/inbox'],
            ['/admin/inbox/close'],
            ['/admin/logs'],
            ['/admin/logs/whereip'],
            ['/admin/logs/delete'],
            ['/admin/logs/readed/all'],
            ['/admin/media/browser'],
            ['/admin/terminal'],
            ['/admin/todos'],
            ['/admin/todos/completed'],
            ['/admin/todos/close'],
            ['/admin/visitors'],
            ['/admin/visitors/delete'],
            ['/admin/visitors/ban'],
            ['/admin/visitors/unban'],
            ['/admin/email/send'],
        ];
    }

    /**
     * Test non-authenticated admin redirect
     *
     * @dataProvider provideAdminUrls
     *
     * @param string $url The admin route URL
     *
     * @return void
     */
    public function testNonAuthAdminRedirect(string $url): void
    {
        $this->client->request('GET', $url);

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }
}