<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Manager\VisitorManager;
use App\Form\PasswordChangeFormType;
use App\Form\UsernameChangeFormType;
use App\Form\ProfilePicChangeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Account settings controller provides user account changes
    Configurable values: username, password, profile-pic
*/

class AccountSettingsController extends AbstractController
{
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private VisitorManager $visitorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AuthManager $authManager, 
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        VisitorManager $visitorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorManager = $visitorManager;
    }

    #[Route('/admin/account/settings', name: 'admin_account_settings_table')]
    public function accountSettingsTable(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/account-settings.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // account settings froms data
                'profile_pic_change_form' => null,
                'username_change_form' => null,
                'password_change_form' => null,
                'error_msg' => null
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/account/settings/pic', name: 'admin_account_settings_pic_change')]
    public function accountSettingsPicChange(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // default error msg
            $error_msg = null;

            // init user entity
            $user = new User();

            // create pic form change
            $form = $this->createForm(ProfilePicChangeFormType::class, $user);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get image
                $image = $form->get('profile-pic')->getData();

                // get file extension
                $extension = $image->getClientOriginalExtension();

                // check if file is image
                if ($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png') {

                    // get image content
                    $fileContents = file_get_contents($image);

                    // encode image
                    $image_code = base64_encode($fileContents);

                    // get user repository
                    $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

                    // update profile pics
                    try {
                        $userRepo->setProfilePic($image_code);
                        $this->entityManager->flush();

                        // redirect back to values table
                        return $this->redirectToRoute('admin_account_settings_table');
                    } catch (\Exception $e) {
                        return $this->errorManager->handleError('error to upload profile pic: '.$e->getMessage(), 500);
                    }  
                } else {
                    $error_msg = 'please select image file';
                }
            }

            // render profile pic change form
            return $this->render('admin/account-settings.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // account settings froms data
                'profile_pic_change_form' => $form->createView(),
                'username_change_form' => null,
                'password_change_form' => null,
                'error_msg' => $error_msg
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/account/settings/username', name: 'admin_account_settings_username_change')]
    public function accountSettingsUsernameChange(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // default error msg
            $error_msg = null;

            // init user entity
            $user = new User();

            // create username form change
            $form = $this->createForm(UsernameChangeFormType::class, $user);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get username 
                $username = $form->get('username')->getData();

                // escape username (XSS protection)
                $username = $this->securityUtil->escapeString($username);

                // get user repository
                $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

                // update username
                try {
                    $userRepo->setUsername($username);
                    $this->entityManager->flush();

                    // redirect back to values table
                    return $this->redirectToRoute('admin_account_settings_table');

                } catch (\Exception $e) {
                    return $this->errorManager->handleError('error to upload profile pic: '.$e->getMessage(), 500);
                }  
            }
            
            // render username change form
            return $this->render('admin/account-settings.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // account settings froms data
                'profile_pic_change_form' => null,
                'password_change_form' => null,
                'username_change_form' => $form,
                'error_msg' => $error_msg
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/account/settings/password', name: 'admin_account_settings_password_change')]
    public function accountSettingsPasswordChange(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // default error msg
            $error_msg = null;

            // init user entity
            $user = new User();

            // create username form change
            $form = $this->createForm(PasswordChangeFormType::class, $user);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get passwords
                $password = $form->get('password')->getData();
                $repassword = $form->get('repassword')->getData();

                // escape data (XSS protection)
                $password = $this->securityUtil->escapeString($password);
                $repassword = $this->securityUtil->escapeString($repassword);

                // get user repository
                $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

                if ($password != $repassword) {
                    $error_msg = 'Your passwords is not match!';
                } else {
                    // update username
                    try {
                        
                        // hash password
                        $password_hash = $this->securityUtil->gen_bcrypt($password, 10);

                        // update password
                        $userRepo->setPassword($password_hash);

                        // flush user data
                        $this->entityManager->flush();

                        // redirect back to values table
                        return $this->redirectToRoute('admin_account_settings_table');

                    } catch (\Exception $e) {
                        return $this->errorManager->handleError('error to upload profile pic: '.$e->getMessage(), 500);
                    }  
                }
            }

            // render password change form
            return $this->render('admin/account-settings.html.twig', [
                // component properties
                'is_mobile' => $this->visitorManager->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // account settings froms data
                'profile_pic_change_form' => null,
                'username_change_form' => null,
                'password_change_form' => $form,
                'error_msg' => $error_msg
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
