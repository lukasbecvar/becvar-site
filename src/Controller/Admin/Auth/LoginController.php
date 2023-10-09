<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Form\LoginFormType;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Login controller provides login function
*/

class LoginController extends AbstractController
{
    private $logHelper;
    private $authManager;
    private $securityUtil;
    private $visitorInfoUtil;

    public function __construct(
        LogHelper $logHelper, 
        AuthManager $authManager, 
        SecurityUtil $securityUtil,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->logHelper = $logHelper;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');   
        } else {

            // default error msg
            $error_msg = null;

            // create user entity
            $user = new User();

            // create register form
            $form = $this->createForm(LoginFormType::class, $user);

            // processing an HTTP request
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get form data
                $username = $form->get('username')->getData();
                $password = $form->get('password')->getData();

                // get remember status
                $remember = $form->get('remember')->getData();

                // escape values (XSS protection)
                $username = $this->securityUtil->escapeString($username);
                $password = $this->securityUtil->escapeString($password);

                // check if user exist
                if ($this->authManager->getUserRepository(["username" => $username]) != null) {
                    
                    // get user data
                    $user = $this->authManager->getUserRepository(["username" => $username]);

                    // check if password valid
                    if ($this->securityUtil->hash_validate($password , $user->getPassword())) {

                        // set user token (login-token session)
                        $this->authManager->login($username, $user->getToken(), $remember);

                    } else { // invalid password error
                        $this->logHelper->log('authenticator', 'trying to login with: '.$username.':'.$password);
                        $error_msg = 'Incorrect username or password.';
                    }
                } else { // user not exist error
                    $this->logHelper->log('authenticator', 'trying to login with: '.$username.':'.$password);
                    $error_msg = 'Incorrect username or password.';
                }

                // redirect to admin
                if ($error_msg == null) {
                    return $this->redirectToRoute('admin_dashboard');
                }
            }

            // get if users empty value
            $users_empty = $this->authManager->isUsersEmpty();

            // render default login view
            return $this->render('admin/auth/login.html.twig', [
                'error_msg' => $error_msg,
                'is_users_empty' => $users_empty,
                'login_form' => $form->createView()
            ]);
        }
    }
}
