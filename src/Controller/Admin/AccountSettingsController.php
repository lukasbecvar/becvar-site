<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Util\SecurityUtil;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use App\Form\PasswordChangeFormType;
use App\Form\UsernameChangeFormType;
use App\Form\ProfilePicChangeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AccountSettingsController
 *
 * Account settings controller provides user account changes.
 * Configurable values: username, password, profile-pic
 *
 * @package App\Controller\Admin
 */
class AccountSettingsController extends AbstractController
{
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Display account settings table.
     *
     * @return Response
     */
    #[Route('/admin/account/settings', methods: ['GET'], name: 'admin_account_settings_table')]
    public function accountSettingsTable(): Response
    {
        return $this->render('admin/account-settings.html.twig', [
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
    }

    /**
     * Change of profile picture in the admin account settings.
     *
     * @param Request $request The request object.
     * @return Response Returns a Response object representing the HTTP response.
     *
     * @throws \Exception Throws an exception if there is an error during the profile picture upload.
     */
    #[Route('/admin/account/settings/pic', methods: ['GET', 'POST'], name: 'admin_account_settings_pic_change')]
    public function accountSettingsPicChange(Request $request): Response
    {
        // init default resources
        $user = new User();
        $errorMsg = null;

        // create pic form change
        $form = $this->createForm(ProfilePicChangeFormType::class, $user);
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get image data
            $image = $form->get('profile-pic')->getData();
            $extension = $image->getClientOriginalExtension();

            // check if file is image
            if ($extension == 'jpg' or $extension == 'jpeg' or $extension == 'png') {
                // get user repository
                $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

                // get image content
                $fileContents = file_get_contents($image);

                // encode image
                $imageCode = base64_encode($fileContents);

                try {
                    // update profile pics
                    $userRepo->setProfilePic($imageCode);
                    $this->entityManager->flush();

                    // redirect back to values table
                    return $this->redirectToRoute('admin_account_settings_table');
                } catch (\Exception $e) {
                    return $this->errorManager->handleError('error to upload profile pic: ' . $e->getMessage(), 500);
                }
            } else {
                $errorMsg = 'please select image file';
            }
        }

        return $this->render('admin/account-settings.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // account settings froms data
            'profile_pic_change_form' => $form->createView(),
            'username_change_form' => null,
            'password_change_form' => null,
            'error_msg' => $errorMsg
        ]);
    }

    /**
     * Change of username in the admin account settings.
     *
     * @param Request $request The request object.
     * @return Response Returns a Response object representing the HTTP response.
     *
     * @throws \Exception Throws an exception if there is an error during the username update.
     */
    #[Route('/admin/account/settings/username', methods: ['GET', 'POST'], name: 'admin_account_settings_username_change')]
    public function accountSettingsUsernameChange(Request $request): Response
    {
        // init default resources
        $user = new User();
        $errorMsg = null;

        // create username form change
        $form = $this->createForm(UsernameChangeFormType::class, $user);
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get username
            $username = $form->get('username')->getData();

            // get user repository
            $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

            try { // update username
                $userRepo->setUsername($username);
                $this->entityManager->flush();

                // redirect back to values table
                return $this->redirectToRoute('admin_account_settings_table');
            } catch (\Exception $e) {
                return $this->errorManager->handleError('error to upload profile pic: ' . $e->getMessage(), 500);
            }
        }

        return $this->render('admin/account-settings.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // account settings froms data
            'profile_pic_change_form' => null,
            'password_change_form' => null,
            'username_change_form' => $form,
            'error_msg' => $errorMsg
        ]);
    }

    /**
     * Change of password in the admin account settings.
     *
     * @param Request $request The request object.
     * @return Response Returns a Response object representing the HTTP response.
     *
     * @throws \Exception Throws an exception if there is an error during the password update.
     */
    #[Route('/admin/account/settings/password', methods: ['GET', 'POST'], name: 'admin_account_settings_password_change')]
    public function accountSettingsPasswordChange(Request $request): Response
    {
        // init default resources
        $user = new User();
        $errorMsg = null;

        // create username form change
        $form = $this->createForm(PasswordChangeFormType::class, $user);
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get passwords
            $password = $form->get('password')->getData();
            $rePassword = $form->get('repassword')->getData();

            // get user repository
            $userRepo = $this->authManager->getUserRepository(['username' => $this->authManager->getUsername()]);

            if ($password != $rePassword) {
                $errorMsg = 'Your passwords is not match!';
            } else {
                try {
                    // hash password
                    $passwordHash = $this->securityUtil->genBcryptHash($password, 10);

                    // update password
                    $userRepo->setPassword($passwordHash);

                    // flush user data
                    $this->entityManager->flush();

                    return $this->redirectToRoute('admin_account_settings_table');
                } catch (\Exception $e) {
                    return $this->errorManager->handleError('error to upload profile pic: ' . $e->getMessage(), 500);
                }
            }
        }

        // render password change form
        return $this->render('admin/account-settings.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // account settings froms data
            'profile_pic_change_form' => null,
            'username_change_form' => null,
            'password_change_form' => $form,
            'error_msg' => $errorMsg
        ]);
    }
}
