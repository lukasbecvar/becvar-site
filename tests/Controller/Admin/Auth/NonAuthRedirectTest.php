<?php

namespace App\Tests\Controller\Auth;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class NonAuthRedirectTest
 *
 * Non-auth redirect authenticator test
 * Test all admin routes in the default state when the user is not logged in
 *
 * @package App\Tests\Controller\Auth
 */
class NonAuthRedirectTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * Auth required routes list
     *
     * @return array<array<string>>
     */
    private const ROUTES = [
        'admin' => [
            '/admin',
            'register',
            '/admin/dashboard'
        ],
        'database_browser' => [
            '/admin/database',
            '/admin/database/add',
            '/admin/database/edit',
            '/admin/database/table',
            '/admin/database/delete'
        ],
        'admin_inbox' => [
            '/admin/inbox',
            '/admin/inbox/close'
        ],
        'admin_logs' => [
            '/admin/logs',
            '/admin/logs/delete',
            '/admin/logs/whereip',
            '/admin/logs/readed/all'
        ],
        'admin_visitors' => [
            '/admin/visitors',
            '/admin/visitors/ban',
            '/admin/visitors/unban',
            '/admin/visitors/delete',
            '/admin/visitors/metrics',
            '/admin/visitors/download'
        ],
        'account_settings' => [
            '/admin/account/settings',
            '/admin/account/settings/pic',
            '/admin/account/settings/username',
            '/admin/account/settings/password'
        ]
    ];

    /**
     * Auth required routes list
     *
     * @return array<array<string>>
     */
    protected function provideAdminUrls(): array
    {
        $urls = [];
        foreach (self::ROUTES as $category => $routes) {
            foreach ($routes as $route) {
                $urls[] = [$route];
            }
        }
        return $urls;
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
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

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertTrue($this->client->getResponse()->isRedirect('/login'));
    }
}
