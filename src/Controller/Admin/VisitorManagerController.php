<?php

namespace App\Controller\Admin;

use App\Manager\AuthManager;
use App\Manager\BanManager;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/*
    Visitor manager controller provides view/ban/delete visitor
*/

class VisitorManagerController extends AbstractController
{
    private $banManager;
    private $authManager;
    private $securityUtil;
    private $visitorInfoUtil;

    public function __construct(
        BanManager $banManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->banManager = $banManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/visitors/{page}', name: 'admin_visitor_manager')]
    public function view(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            return $this->render('admin/visitors-manager.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,

                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),

                // visitor manager data
                'page' => $page,
                'banned_count' => $this->banManager->getBannedCount(),
                'visitors_limit' => $_ENV['ITEMS_PER_PAGE'],
                'visitors_data' => $this->visitorInfoUtil->getVisitors($page),
                'visitors_count' => $this->visitorInfoUtil->getVisitorsCount($page)
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/visitors/delete/{page}', name: 'admin_visitor_delete')]
    public function delete(int $page): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            return $this->render('admin/elements/confirmation/delete-visitors.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,
    
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),
    
                // delete confirmation data
                'page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    } 

    #[Route('/admin/visitors/ban/{id}/{page}', name: 'admin_visitor_ban')]
    public function ban(int $id, int $page, Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // check if ban submited
            if (isset($_POST['submitBan'])) {
                if (!empty($_POST['banReason'])) {
                    $reason = $this->securityUtil->escapeString($_POST['banReason']);
                } else {
                    $reason = 'no-reason';
                }

                // get visitor ip
                $ip_address = $this->banManager->getVisitorIP($id);

                // ban visitor
                $this->banManager->banVisitor($ip_address, $reason);

                return $this->redirectToRoute('admin_visitor_manager', [
                    'page' => $page
                ]);
            }

            return $this->render('admin/elements/forms/ban-form.html.twig', [
                // component properties
                'is_mobile' => $this->visitorInfoUtil->isMobile(),
                'is_dashboard' => false,
    
                // user data
                'user_name' => $this->authManager->getUsername(),
                'user_role' => $this->authManager->getUserRole(),
                'user_pic' => $this->authManager->getUserProfilePic(),
    
                // ban form data
                'ban_id' => $id,
                'return_page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/visitors/unban/{id}/{page}', name: 'admin_visitor_unban')]
    public function unban(int $id, int $page, Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // get visitor ip
            $ip_address = $this->banManager->getVisitorIP($id);
            
            // check if banned
            if ($this->banManager->isVisitorBanned($ip_address)) {

                // unban visitor
                $this->banManager->unbanVisitor($ip_address);
            }

            return $this->redirectToRoute('admin_visitor_manager', [
                'page' => $page
            ]);    

        } else {
            return $this->redirectToRoute('auth_login');
        }
    }
}
