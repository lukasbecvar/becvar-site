<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Entity\Visitor;
use App\Form\BanFormType;
use App\Util\SecurityUtil;
use App\Manager\BanManager;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Visitor manager controller provides view/ban/delete visitor
*/

class VisitorManagerController extends AbstractController
{
    private SiteUtil $siteUtil;
    private BanManager $banManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        BanManager $banManager,
        AuthManager $authManager,
        SecurityUtil $securityUtil,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->banManager = $banManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/admin/visitors', name: 'admin_visitor_manager')]
    public function visitorsTable(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get page
            $page = intval($this->siteUtil->getQueryString('page', $request));

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
                'current_ip' => $this->visitorInfoUtil->getIP(),
                'online_count' => count($this->visitorManager->getVisitorsWhereStstus('online')),
                'banned_count' => $this->banManager->getBannedCount(),
                'visitors_limit' => $_ENV['ITEMS_PER_PAGE'],
                'visitors_data' => $this->visitorManager->getVisitors($page),
                'visitors_count' => $this->visitorManager->getVisitorsCount($page)
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/visitors/delete', name: 'admin_visitor_delete')]
    public function deleteAllVisitors(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // get page
            $page = $this->siteUtil->getQueryString('page', $request);

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

    #[Route('/admin/visitors/ban', name: 'admin_visitor_ban')]
    public function banVisitor(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // get query parameters
            $page = intval($this->siteUtil->getQueryString('page', $request));
            $id = intval($this->siteUtil->getQueryString('id', $request));

            // create user entity
            $visitor = new Visitor();

            // create register form
            $form = $this->createForm(BanFormType::class, $visitor);

            // processing an HTTP request
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get reason
                $ban_reason = $form->get('ban_reason')->getData();

                // check if reason set
                if (!empty($ban_reason)) {
                    $reason = $this->securityUtil->escapeString($ban_reason);
                } else {
                    $reason = 'no-reason';
                }

                // get visitor ip
                $ip_address = $this->banManager->getVisitorIP($id);

                // ban visitor
                $this->banManager->banVisitor($ip_address, $reason);

                // check if banned by inbox
                if ($request->query->get('referer') == 'inbox') {
                    return $this->redirectToRoute('admin_inbox', [
                        'page' => $page
                    ]);              
                }

                // redirect back to visitor page
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
                'ban_form' => $form,
                'return_page' => $page
            ]);
        } else {
            return $this->redirectToRoute('auth_login');
        }
    }

    #[Route('/admin/visitors/unban', name: 'admin_visitor_unban')]
    public function unbanVisitor(Request $request): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            
            // get query parameters
            $page = intval($this->siteUtil->getQueryString('page', $request));
            $id = intval($this->siteUtil->getQueryString('id', $request));

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
