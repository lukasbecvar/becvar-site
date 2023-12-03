<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Form\RegisterFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Register controller provides user register function.
 * Note: This function is enabled only if users table is empty or for owner users
 * Note: Login uses its own authenticator, not Symfony auth.
 */
class RegisterController extends AbstractController
{
    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /**
     * RegisterController constructor.
     *
     * @param AuthManager            $authManager
     * @param SecurityUtil           $securityUtil
     */
    public function __construct(
        AuthManager $authManager, 
        SecurityUtil $securityUtil, 
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles user registration.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/register', methods: ['GET', 'POST'], name: 'auth_register')]
    public function register(Request $request): Response
    {
        // check if user table is empty or if registrant is admin
        if (!$this->authManager->isRegisterPageAllowed()) {
            return $this->redirectToRoute('auth_login');   
        } else {
            // init user enity
            $user = new User();

            // default error msg
            $error_msg = null;

            // create register form
            $form = $this->createForm(RegisterFormType::class, $user);
            
            // processing an HTTP request
            $form->handleRequest($request);

            // check if form submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get form data
                $username = $form->get('username')->getData();
                $password = $form->get('password')->getData();
                $repassword = $form->get('re-password')->getData();

                // escape values (XSS protection)
                $username = $this->securityUtil->escapeString($username);
                $password = $this->securityUtil->escapeString($password);
                $repassword = $this->securityUtil->escapeString($repassword);

                // check if username used
                if ($this->authManager->getUserRepository(['username' => $username]) != null) {
                    $error_msg = 'This username is already in use';
                } else {

                    // check if passwords not match
                    if ($password != $repassword) {
                        $error_msg = 'Your passwords dont match';
                    } else {

                        // add new user to database
                        $this->authManager->registerNewUser($username, $password);
                            
                        // redirect to homepage
                        return $this->redirectToRoute('admin_dashboard');
                    }
                }
            }

            // render default register view
            return $this->render('admin/auth/register.html.twig', [
                'error_msg' => $error_msg,
                'registration_form' => $form->createView()
            ]);
        }
    }
}
