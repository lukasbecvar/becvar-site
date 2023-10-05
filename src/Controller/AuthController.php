<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Auth controller provides register & login component
*/

class AuthController extends AbstractController
{
    private $logHelper;
    private $authManager;
    private $securityUtil;
    private $entityManager;
    private $visitorInfoUtil;  

    public function __construct(
        LogHelper $logHelper, 
        AuthManager $authManager, 
        SecurityUtil $securityUtil, 
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        // check if submited
        if ($form->isSubmitted() && $form->isValid()) {

            // get current date
            $date = date('d.m.Y H:i:s');

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
                return $this->render('admin/register.html.twig', [
                    'errorMSG' => 'This username is already in use',
                    'registrationForm' => $form->createView(),
                ]);
            }

            // check if not match
            if ($password != $repassword) {
                return $this->render('admin/register.html.twig', [
                    'errorMSG' => 'Your passwords dont match',
                    'registrationForm' => $form->createView(),
                ]);
            }

            // get user ip
            $ip_address = $this->visitorInfoUtil->getIP();

            // generate token
            $token = ByteString::fromRandom(32)->toString();
            
            // get visitor id
            $visitor_id = $this->visitorInfoUtil->getVisitorID($ip_address);

            // password hash
            $hashed_password = $this->securityUtil->gen_bcrypt($password, 10);

            // set from data
            $user->setUsername($username);
            $user->setPassword($hashed_password);

            // set dates
            $user->setRegistedTime($date);
            $user->setLastLoginTime('not logged');

            // set profile pics (base64)
            $user->setProfilePic('base64 image');

            // set visitor id
            $user->setVisitorId($visitor_id); 

            // set others
            $user->setToken($token);
            $user->setRole("user");
            $user->setIpAddress($ip_address);

            // log regstration event
            $this->logHelper->log('authenticator', 'registration new user: '.$username.' registred');

            // insert new user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set user token (login-token session)
            $this->authManager->login($username, $user->getToken(), false);

            // redirect to homepage
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/register.html.twig', [
            'errorMSG' => null,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // logout user (is session found)
        if ($this->authManager->isUserLogedin()) {
            $this->authManager->logout();
        }

        return $this->redirectToRoute('home');
    }
}
