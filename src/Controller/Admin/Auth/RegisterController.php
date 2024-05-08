<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Manager\AuthManager;
use App\Form\RegisterFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class RegisterController
 *
 * Register controller provides user register function.
 * Note: This function is enabled only if users table is empty or for owner users
 * Note: Login uses its own authenticator, not Symfony auth.
 *
 * @package App\Controller\Admin\Auth
 */
class RegisterController extends AbstractController
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
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
        }

        // init default resources
        $user = new User();
        $error_msg = null;

        // create register form
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        // check if form submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get form data
            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();
            $repassword = $form->get('re-password')->getData();

            // check if username used
            if ($this->authManager->getUserRepository(['username' => $username]) != null) {
                $error_msg = 'This username is already in use';
            } else {
                // check if passwords not match
                if ($password != $repassword) {
                    $error_msg = 'Your passwords dont match';
                } else {
                    $this->authManager->registerNewUser($username, $password);
                    return $this->redirectToRoute('admin_dashboard');
                }
            }
        }

        return $this->render('admin/auth/register.html.twig', [
            'error_msg' => $error_msg,
            'registration_form' => $form->createView()
        ]);
    }
}
