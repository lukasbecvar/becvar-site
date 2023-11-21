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

/*
    Login controller provides user login function
    ! Login uses its own authenticator not symfony auth !
*/

class LoginController extends AbstractController
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;

    public function __construct(LogManager $logManager, AuthManager $authManager, SecurityUtil $securityUtil) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
    }

    #[Route('/login', name: 'auth_login')]
    public function login(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');   
        } else {
            // default error msg
            $error_msg = null;

            // init user entity
            $user = new User();

            // create register form
            $form = $this->createForm(LoginFormType::class, $user);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get form data
                $username = $form->get('username')->getData();
                $password = $form->get('password')->getData();
                $remember = $form->get('remember')->getData();

                // escape values (XSS protection)
                $username = $this->securityUtil->escapeString($username);
                $password = $this->securityUtil->escapeString($password);

                // check if user exist
                if ($this->authManager->getUserRepository(['username' => $username]) != null) {
                    
                    // get user data
                    $user = $this->authManager->getUserRepository(['username' => $username]);

                    // get user password form database
                    $user_password = $user->getPassword();

                    // check if password valid
                    if ($this->securityUtil->hash_validate($password, $user_password)) {

                        // set user token (login-token session)
                        $this->authManager->login($username, $user->getToken(), $remember);

                    } else { // invalid password error
                        $this->logManager->log('authenticator', 'trying to login with: '.$username.':'.$password);
                        $error_msg = 'Incorrect username or password.';
                    }
                } else { // user not exist error
                    $this->logManager->log('authenticator', 'trying to login with: '.$username.':'.$password);
                    $error_msg = 'Incorrect username or password.';
                }

                // redirect to admin (if login OK)
                if ($error_msg == null) {
                    return $this->redirectToRoute('admin_dashboard');
                }
            }

            // render login view
            return $this->render('admin/auth/login.html.twig', [
                'error_msg' => $error_msg,
                'is_users_empty' => $this->authManager->isRegisterPageAllowed(),
                'login_form' => $form->createView()
            ]);
        }
    }
}
