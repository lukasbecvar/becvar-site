<?php

namespace App\Controller\Admin\Auth;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\ErrorManager;
use App\Form\RegisterFormType;
use App\Manager\VisitorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
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
    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /** * @var VisitorManager */
    private VisitorManager $visitorManager;  

    /** * @var VisitorInfoUtil */
    private VisitorInfoUtil $visitorInfoUtil;

    /** * @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * RegisterController constructor.
     *
     * @param LogManager             $logManager
     * @param AuthManager            $authManager
     * @param ErrorManager           $errorManager
     * @param SecurityUtil           $securityUtil
     * @param VisitorManager         $visitorManager
     * @param VisitorInfoUtil        $visitorInfoUtil
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LogManager $logManager, 
        AuthManager $authManager, 
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Handles user registration.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/register', name: 'auth_register')]
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

                        // get current date
                        $date = date('d.m.Y H:i:s');

                        // get user ip
                        $ip_address = $this->visitorInfoUtil->getIP();
                    
                        // generate token
                        $token = ByteString::fromRandom(32)->toString();
                        
                        // get visitor id
                        $visitor_id = $this->visitorManager->getVisitorID($ip_address);

                        // password hash
                        $hashed_password = $this->securityUtil->gen_bcrypt($password, 10);

                        // default profile pics base64
                        $image_base64 = '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBw4RDQ0OEA0QDhANDQ0NDw4NDhsNDg0OFREWFxcTFRUYICggGBolGxMTITEhJSkrLi4uFx8zODMsNygtLisBCgoKDQ0NDg0NDisZFRkrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrK//AABEIAOYA2wMBIgACEQEDEQH/xAAaAAEAAwEBAQAAAAAAAAAAAAAAAQQFAwIH/8QAMhABAQABAQYEBAQGAwAAAAAAAAECEQMEEiFRkSIxQWEFcYGhQnKxwSMyUoLh8DNi0f/EABUBAQEAAAAAAAAAAAAAAAAAAAAB/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8A+qAKgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIebtMf6p3B7HObXH+qd49ygkQkAAAAAAAAAAAAAAAAAAAEWgjLKSa26SKe232/hn1v/jhvG3uV9vSfu5A9Z7TK+eVv1eNEiiNHrHKzytnyqAFnZb5lPPxT7r2y2kyxlmul6shY3Ta2Zaa8ulvJBpCEgAAAAAAAAAAAAAAAAK2/bSTCzXnfT10WMrpLb6c/oyNpncsrlfX7QHkBQAAAAdN2kueOt05uYDZSr7nteLDn5zlVhAAAAAAAAAAAAAAAABX37LTC+9mP+9mau/EbywnvapAAKAAAAAALPw/LxWdcf0aLL3O/wATH31n2aiAAAAAAAAAAAAAAADjvW14cdZ53lAVfiF8WP5f3VXrabS5XW3V5UAAAAAAAAdN3v8AEw/NGqxpdLrPTmv7nvFytmXPSayoLYAAAAAAAAAAAAACp8Qnhntl+y28bXCZY2X1BkD1tMLjdLNHlQAAAAAAAAWdwnjvtjVaRpbnseHHn53z9vZB3SAAAAAAAAAAAAACEgK2/wD/AB/3Ys5o7/PB/dGcAAoAAAAAAtfD74svy/u0FD4dj4sr6Sad19BCQAAAAAAAAAAAAAABz281wyn/AFrJbNjHzx0tnS6AgBQAAAAAkBf+Hzw29clpz3fDhwxl8/V1QAAAAAAAAAAAAAAAAFLf9l5ZSeXnp0XUWAxha2+52S2XWTW6XlZFVQAAAAWNy2VuUvpOf1eNhsLneknnWls8JjJJ5T7+6D0kAAAAAAAAAAAAAAQCRFrxdrjPxTuDoOGW94T8Wvyjllv2Ppjb9gd95vgy+TKd9tvWWUs0klcFAAAAF74deWU95+i4ydhtrjrppz6rOO/T1x7VBdFeb5h1s+ce8dvhfxQHUeZlOsv1egAAAAAAAAAU983jTwzz9b09gdNvvWOPL+a9J6fNT2m9Z3109pycQC29UaJFAAAAAAAAAAAAB0w2+c8sr8rzjmAvbHfZeWU0955f4W5WMsbrvHDdL/Lfsg0hCQAAAAc9vtOHG325fNk2+t875rvxDK+HGS9byU+G9L2BAnhvS9jhvS9lECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYF/cNrrjcb54/otMzdLcc5yvPleXVpoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=';

                        // set user entity data
                        $user->setUsername($username);
                        $user->setPassword($hashed_password);
                        $user->setRole('Owner');
                        $user->setIpAddress($ip_address);
                        $user->setToken($token);
                        $user->setRegistedTime($date);
                        $user->setLastLoginTime('not logged');
                        $user->setProfilePic($image_base64);
                        $user->setVisitorId(strval($visitor_id));

                        // log registration event
                        $this->logManager->log('authenticator', 'registration new user: '.$username.' registred');

                        // insert new user
                        try {
                            $this->entityManager->persist($user);
                            $this->entityManager->flush();
                        } catch (\Exception $e) {
                            return $this->errorManager->handleError('error to register new user: '.$e->getMessage(), 400);
                        }

                        // set user token (login-token session)
                        if (!$this->authManager->isUserLogedin()) {
                            $this->authManager->login($username, $user->getToken(), false);
                        }
                            
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
