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

/**
 * Class VisitorManagerController
 * 
 * Visitor manager controller provides view/ban/delete visitor.
 * 
 * @package App\Controller\Admin
 */
class VisitorManagerController extends AbstractController
{
    /**
     * @var SiteUtil
     * Instance of the SiteUtil for handling site-related utilities.
     */
    private SiteUtil $siteUtil;

    /**
     * @var BanManager
     * Instance of the BanManager for handling ban-related functionality.
     */
    private BanManager $banManager;

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
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var VisitorInfoUtil
     * Instance of the VisitorInfoUtil for handling visitor information-related utilities.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * VisitorManagerController constructor.
     *
     * @param SiteUtil        $siteUtil
     * @param BanManager      $banManager
     * @param AuthManager     $authManager
     * @param SecurityUtil    $securityUtil
     * @param VisitorManager  $visitorManager
     * @param VisitorInfoUtil $visitorInfoUtil
     */
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

    /**
     * Display the table of visitors and their details.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/visitors', methods: ['GET'], name: 'admin_visitor_manager')]
    public function visitorsTable(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            // get page int
            $page = intval($this->siteUtil->getQueryString('page', $request));

            return $this->render('admin/visitors-manager.html.twig', [
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

    /**
     * Display the confirmation form for deleting all visitors.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/visitors/delete', methods: ['GET'], name: 'admin_visitor_delete')]
    public function deleteAllVisitors(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            // get page int
            $page = $this->siteUtil->getQueryString('page', $request);

            return $this->render('admin/elements/confirmation/delete-visitors.html.twig', [
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

    /**
     * Ban a visitor.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/visitors/ban', methods: ['GET', 'POST'], name: 'admin_visitor_ban')]
    public function banVisitor(Request $request): Response
    {
        if ($this->authManager->isUserLogedin()) {
            // create user entity
            $visitor = new Visitor();

            // get query parameters
            $page = intval($this->siteUtil->getQueryString('page', $request));
            $id = intval($this->siteUtil->getQueryString('id', $request));

            // create register form
            $form = $this->createForm(BanFormType::class, $visitor);
            $form->handleRequest($request);

            // check form if submited
            if ($form->isSubmitted() && $form->isValid()) {

                // get ban reason
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

    /**
     * Unban a visitor.
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/visitors/unban', methods: ['GET'], name: 'admin_visitor_unban')]
    public function unbanVisitor(Request $request): Response
    {
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
