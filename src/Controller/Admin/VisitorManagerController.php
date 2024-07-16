<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Entity\Visitor;
use App\Form\BanFormType;
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
 * Visitor manager controller provides view/ban/delete visitor
 *
 * @package App\Controller\Admin
 */
class VisitorManagerController extends AbstractController
{
    private SiteUtil $siteUtil;
    private BanManager $banManager;
    private AuthManager $authManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        SiteUtil $siteUtil,
        BanManager $banManager,
        AuthManager $authManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->siteUtil = $siteUtil;
        $this->banManager = $banManager;
        $this->authManager = $authManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Display the table of visitors and their details
     *
     * @param Request $request object containing the page number
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/admin/visitors', methods: ['GET'], name: 'admin_visitor_manager')]
    public function visitorsTable(Request $request): Response
    {
        // get page int
        $page = intval($this->siteUtil->getQueryString('page', $request));

        // get filter value
        $filter = $this->siteUtil->getQueryString('filter', $request);

        // return visitor manager view
        return $this->render('admin/visitors-manager.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // visitor manager data
            'page' => $page,
            'filter' => $filter,
            'visitorInfoData' => null,
            'visitorsLimit' => $_ENV['ITEMS_PER_PAGE'],
            'currentIp' => $this->visitorInfoUtil->getIP(),
            'bannedCount' => $this->banManager->getBannedCount(),
            'onlineVisitors' => $this->visitorManager->getOnlineVisitorIDs(),
            'visitorsCount' => $this->visitorManager->getVisitorsCount($page),
            'visitorsData' => $this->visitorManager->getVisitors($page, $filter)
        ]);
    }

    /**
     * Provides IP information for a given IP address to the admin panel
     *
     * @param Request $request The request object containing the IP address
     *
     * @return Response A response object containing the IP information
     */
    #[Route('/admin/visitors/ipinfo', methods: ['GET'], name: 'admin_visitor_ipinfo')]
    public function visitorIpInfo(Request $request): Response
    {
        // get ip address from query string
        $ipAddress = $this->siteUtil->getQueryString('ip', $request);

        // check if ip parameter found
        if ($ipAddress == 1) {
            return $this->redirectToRoute('admin_visitor_manager');
        }

        // get ip info
        $ipInfoData = $this->visitorInfoUtil->getIpInfo($ipAddress);
        $ipInfoData = json_decode(json_encode($ipInfoData), true);

        // return visitor manager view
        return $this->render('admin/visitors-manager.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // visitor manager data
            'page' => 1,
            'filter' => 1,
            'currentIp' => $ipAddress,
            'visitorInfoData' => $ipInfoData,
            'bannedCount' => $this->banManager->getBannedCount(),
            'onlineVisitors' => $this->visitorManager->getOnlineVisitorIDs()
        ]);
    }

    /**
     * Display the confirmation form for deleting all visitors
     *
     * @param Request $request object containing the page number
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/admin/visitors/delete', methods: ['GET'], name: 'admin_visitor_delete')]
    public function deleteAllVisitors(Request $request): Response
    {
        // get page int
        $page = $this->siteUtil->getQueryString('page', $request);

        // return delete confirmation view
        return $this->render('admin/elements/confirmation/delete-visitors.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // delete confirmation data
            'page' => $page
        ]);
    }

    /**
     * Ban a visitor
     *
     * @param Request $request object representing the HTTP request
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/admin/visitors/ban', methods: ['GET', 'POST'], name: 'admin_visitor_ban')]
    public function banVisitor(Request $request): Response
    {
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
            $banReason = $form->get('ban_reason')->getData();

            // check if reason set
            if (empty($banReason)) {
                $banReason = 'no-reason';
            }

            // get visitor ip
            $ipAddress = $this->banManager->getVisitorIP($id);

            // ban visitor
            $this->banManager->banVisitor($ipAddress, $banReason);

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

        // render ban form
        return $this->render('admin/elements/forms/ban-form.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            // ban form data
            'banForm' => $form
        ]);
    }

    /**
     * Unban a visitor
     *
     * @param Request $request object representing the HTTP request
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/admin/visitors/unban', methods: ['GET'], name: 'admin_visitor_unban')]
    public function unbanVisitor(Request $request): Response
    {
        // get query parameters
        $page = intval($this->siteUtil->getQueryString('page', $request));
        $id = intval($this->siteUtil->getQueryString('id', $request));

        // get visitor ip
        $ipAddress = $this->banManager->getVisitorIP($id);

        // check if banned
        if ($this->banManager->isVisitorBanned($ipAddress)) {
            // unban visitor
            $this->banManager->unbanVisitor($ipAddress);
        }

        // redirect back to visitor page
        return $this->redirectToRoute('admin_visitor_manager', [
            'page' => $page
        ]);
    }
}
