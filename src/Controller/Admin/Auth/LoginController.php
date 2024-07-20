<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Form\LoginFormType;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LoginController
 *
 * Login controller provides user login function
 * Note: Login uses its own authenticator (not Symfony auth)
 *
 * @package App\Controller\Admin\Auth
 */
class LoginController extends AbstractController
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;

    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * User login handler
     *
     * @param Request $request object representing the HTTP request
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/login', methods: ['GET', 'POST'], name: 'auth_login')]
    public function login(Request $request): Response
    {
        // check if user is already loggedin
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // init default resources
        $user = new User();
        $errorMsg = null;

        // create register form
        $form = $this->createForm(LoginFormType::class, $user);
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get form data
            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();
            $remember = $form->get('remember')->getData();

            // get user data
            $userData = $this->authManager->getUserRepository(['username' => $username]);

            // check if user exist
            if ($userData != null) {
                // get user password form database
                $userPassword = $userData->getPassword();

                // check if password valid
                if ($this->securityUtil->verifyPassword($password, $userPassword)) {
                    $this->authManager->login($username, $userData->getToken(), $remember);
                } else { // invalid password error
                    $this->logManager->log(
                        name: 'authenticator',
                        value: 'trying to login with: ' . $username . ':' . $password
                    );
                    $errorMsg = 'Incorrect username or password.';
                }
            } else { // user not exist error
                $this->logManager->log(
                    name: 'authenticator',
                    value: 'trying to login with: ' . $username . ':' . $password
                );
                $errorMsg = 'Incorrect username or password.';
            }

            // redirect to admin (if login OK)
            if ($errorMsg == null && $this->authManager->isUserLogedin()) {
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        // render login view
        return $this->render('admin/auth/login.twig', [
            'errorMsg' => $errorMsg,
            'isUsersEmpty' => $this->authManager->isRegisterPageAllowed(),
            'loginForm' => $form->createView()
        ]);
    }
}
