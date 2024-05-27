<?php

namespace App\Tests\Admin;

use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SendEmailTest
 *
 * Test SendEmail controller.
 *
 * @package App\Tests\Admin
 */
class SendEmailTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Create a mock object for AuthManager.
     *
     * @return object
     */
    private function createAuthManagerMock(): object
    {
        $authManagerMock = $this->createMock(AuthManager::class);
        $authManagerMock->method('isUserLogedin')->willReturn(true);

        return $authManagerMock;
    }

    /**
     * Test send email load.
     *
     * @return void
     */
    public function testSendEmailLoad(): void
    {
        $this->client->getContainer()->set(AuthManager::class, $this->createAuthManagerMock());

        // make post request to todo manager controller
        $this->client->request('GET', '/admin/email/send');

        // assert
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('body', 'Send email');
    }
}
