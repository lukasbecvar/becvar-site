<?php

namespace App\Tests\Controller\Admin\Auth;

use App\Tests\CustomTestCase;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Class RegisterControllerTest
 *
 * Test cases for auth register component
 *
 * @package App\Tests\Admin\Auth
 */
class RegisterControllerTest extends CustomTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test load register page with registration not allowed
     *
     * @return void
     */
    public function testLoadRegisterPageWithRegistrationNotAllowed(): void
    {
        // simulate not allow registration
        $this->allowRegistration($this->client, false);

        // load register page
        $this->client->request('GET', '/register');

        // assert response
        $this->assertSelectorNotExists('.form-title');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * Test load register page with registration allowed
     *
     * @return void
     */
    public function testLoadRegisterPageWithRegistrationAllowed(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client);

        // load register page
        $this->client->request('GET', '/register');

        // assert response
        $this->assertSelectorTextContains('title', 'Admin | Login');
        $this->assertSelectorTextContains('.form-title', 'Register admin account');
        $this->assertSelectorExists('form[name="register_form"]');
        $this->assertSelectorExists('input[name="register_form[username]"]');
        $this->assertSelectorExists('input[name="register_form[password]"]');
        $this->assertSelectorExists('input[name="register_form[re-password]"]');
        $this->assertSelectorExists('button:contains("Register")');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test submit register form with empty fields
     *
     * @return void
     */
    public function testSubmitRegisterFormWithEmptyFields(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client, true);

        // submit register form
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => '',
                'password' => '',
                're-password' => ''
            ],
        ]);

        // assert response
        $this->assertSelectorTextContains('li:contains("Please enter a username")', 'Please enter a username');
        $this->assertSelectorTextContains('li:contains("Please enter a password")', 'Please enter a password');
        $this->assertSelectorTextContains('li:contains("Please enter a password again")', 'Please enter a password again');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test submit register form with low length fields
     *
     * @return void
     */
    public function testSubmitRegisterFormWithLowLengthFields(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client, true);

        // submit register form
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => 'a',
                'password' => 'a',
                're-password' => 'a'
            ],
        ]);

        // assert response
        $this->assertSelectorTextContains('li:contains("Your username should be at least 4 characters")', 'Your username should be at least 4 characters');
        $this->assertSelectorTextContains('li:contains("Your password should be at least 8 characters")', 'Your password should be at least 8 characters');
        $this->assertSelectorTextContains('li:contains("Your password again should be at least 8 characters")', 'Your password again should be at least 8 characters');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test submit register form with high length fields
     *
     * @return void
     */
    public function testSubmitRegisterFormWithHighLengthFields(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client, true);

        // submit register form
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => 'awfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeewawfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeew',
                'password' => 'awfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeewawfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeew',
                're-password' => 'awfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeewawfeewfawfeewfawfeewfawfeewfawfeewfawfeewfawfeew'
            ],
        ]);

        // assert response
        $this->assertSelectorTextContains('li:contains("This value is too long. It should have 50 characters or less.")', 'This value is too long. It should have 50 characters or less.');
        $this->assertSelectorTextContains('li:contains("This value is too long. It should have 80 characters or less.")', 'This value is too long. It should have 80 characters or less.');
        $this->assertSelectorTextContains('li:contains("This value is too long. It should have 80 characters or less.")', 'This value is too long. It should have 80 characters or less.');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test submit register form with passwords that do not match
     *
     * @return void
     */
    public function testSubmitRegisterFormWithNotMatchPasswords(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client, true);

        // submit register form
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => 'testing_username',
                'password' => 'testing_password_1',
                're-password' => 'testing_password_2'
            ],
        ]);

        // assert response
        $this->assertSelectorTextContains('body', 'Your passwords dont match');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test submit register form with success
     *
     * @return void
     */
    public function testSubmitRegisterFormWithSuccess(): void
    {
        // simulate allow registration
        $this->allowRegistration($this->client, true);

        // submit register form
        $this->client->request('POST', '/register', [
            'register_form' => [
                'username' => ByteString::fromRandom(16)->toString(),
                'password' => 'testing_password_1',
                're-password' => 'testing_password_1'
            ],
        ]);

        // assert response
        $this->assertResponseRedirects('/admin/dashboard');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
