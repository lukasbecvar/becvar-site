<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Form\LoginFormType;
use App\Service\Manager\LogManager;
use App\Service\Manager\AuthManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class LoginController
 * 
 * Login controller provides user login function.
 * Note: Login uses its own authenticator, not Symfony auth.
 * 
 * @package App\Controller\Admin\Auth
 */
class LoginController extends AbstractController
{
    /**
     * @var LogManager
     * Instance of the LogManager for handling logging functionality.
     */
    private LogManager $logManager;

    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;

    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * LoginController constructor.
     *
     * @param LogManager   $logManager
     * @param AuthManager  $authManager
     * @param SecurityUtil $securityUtil
     */
    public function __construct(LogManager $logManager, AuthManager $authManager, SecurityUtil $securityUtil) 
    {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * User login action.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/login', methods: ['GET', 'POST'], name: 'auth_login')]
    public function login(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            return $this->redirectToRoute('admin_dashboard');   
        } else {
            $user = new User();
            $error_msg = null;

            // create register form
            $form = $this->createForm(LoginFormType::class, $user);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get form data
                $username = $form->get('username')->getData();
                $password = $form->get('password')->getData();
                $remember = $form->get('remember')->getData();
                
                // check if user exist
                if ($this->authManager->getUserRepository(['username' => $username]) != null) {
                    
                    // get user data
                    $user = $this->authManager->getUserRepository(['username' => $username]);

                    // get user password form database
                    $user_password = $user->getPassword();

                    // check if password valid
                    if ($this->securityUtil->hashValidate($password, $user_password)) {
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
