<?php

namespace App\Controller\Admin;

use App\Util\AppUtil;
use App\Entity\Visitor;
use App\Util\ExportUtil;
use App\Form\BanFormType;
use App\Manager\BanManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use App\Form\VisitorListExportType;
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
    private AppUtil $appUtil;
    private ExportUtil $exportUtil;
    private BanManager $banManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        AppUtil $appUtil,
        ExportUtil $exportUtil,
        BanManager $banManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->appUtil = $appUtil;
        $this->exportUtil = $exportUtil;
        $this->banManager = $banManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Display the table of visitors and their details
     *
     * @param Request $request The request object
     *
     * @return Response The visitor manager page view
     */
    #[Route('/admin/visitors', methods: ['GET'], name: 'admin_visitor_manager')]
    public function visitorsTable(Request $request): Response
    {
        // get page int
        $page = intval($this->appUtil->getQueryString('page', $request));

        // get filter value
        $filter = $this->appUtil->getQueryString('filter', $request);

        // return visitor manager view
        return $this->render('admin/visitors-manager.twig', [
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
     * @param Request $request The request object
     *
     * @return Response The IP information view
     */
    #[Route('/admin/visitors/ipinfo', methods: ['GET'], name: 'admin_visitor_ipinfo')]
    public function visitorIpInfo(Request $request): Response
    {
        // get ip address from query string
        $ipAddress = $this->appUtil->getQueryString('ip', $request);

        // check if ip parameter found
        if ($ipAddress == 1) {
            return $this->redirectToRoute('admin_visitor_manager');
        }

        // get ip info
        $ipInfoData = $this->visitorInfoUtil->getIpInfo($ipAddress);
        $ipInfoData = json_decode(json_encode($ipInfoData), true);

        // return visitor manager view
        return $this->render('admin/visitors-manager.twig', [
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
     * @param Request $request The request object
     *
     * @return Response The delete confirmation page view
     */
    #[Route('/admin/visitors/delete', methods: ['GET'], name: 'admin_visitor_delete')]
    public function deleteAllVisitors(Request $request): Response
    {
        // get page int
        $page = $this->appUtil->getQueryString('page', $request);

        // return delete confirmation view
        return $this->render('admin/element/confirmation/delete-visitors.twig', [
            // delete confirmation data
            'page' => $page
        ]);
    }

    /**
     * Ban a visitor
     *
     * @param Request $request The request object
     *
     * @return Response The redirect back to visitor manager
     */
    #[Route('/admin/visitors/ban', methods: ['GET', 'POST'], name: 'admin_visitor_ban')]
    public function banVisitor(Request $request): Response
    {
        // create user entity
        $visitor = new Visitor();

        // get query parameters
        $page = intval($this->appUtil->getQueryString('page', $request));
        $id = intval($this->appUtil->getQueryString('id', $request));

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
        return $this->render('admin/element/form/ban-form.twig', [
            // ban form data
            'banForm' => $form
        ]);
    }

    /**
     * Unban a visitor
     *
     * @param Request $request The request object
     *
     * @return Response The redirect back to visitor manager
     */
    #[Route('/admin/visitors/unban', methods: ['GET'], name: 'admin_visitor_unban')]
    public function unbanVisitor(Request $request): Response
    {
        // get query parameters
        $page = intval($this->appUtil->getQueryString('page', $request));
        $id = intval($this->appUtil->getQueryString('id', $request));

        // get visitor ip
        $ipAddress = $this->banManager->getVisitorIP($id);

        // check if banned
        if ($this->banManager->isVisitorBanned($ipAddress)) {
            // unban visitor
            $this->banManager->unbanVisitor($ipAddress);
        }

        // check if unban init by inbox
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

    /**
     * Export visitors list data to Excel or Pdf file
     *
     * @param Request $request The request object
     *
     * @return Response The export form view
     */
    #[Route('/admin/visitors/download', methods: ['GET', 'POST'], name: 'admin_visitor_manager_download')]
    public function downloadVisitorsList(Request $request): Response
    {
        $errorMsg = null;

        // create form
        $form = $this->createForm(VisitorListExportType::class);
        $form->handleRequest($request);

        // check if form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // get form data
            $filter = $data['filter'];
            $format = $data['format'];

            // check if data is empty
            if ($format == null || $filter == null) {
                $errorMsg = 'Please select a filter and a format';
            }

            // check if format is valid
            if ($format != 'PDF' && $format != 'EXCEL') {
                $errorMsg = 'Please select a valid format';
            }

            // get visitors list
            $visitorsList = $this->visitorManager->getVisitorsByFilter($filter);

            // check if visitors list is empty
            if ($visitorsList == null) {
                $errorMsg = 'no visitors found in selected time period';
            }

            // check if error found
            if ($errorMsg == null) {
                // export data with valid method
                if ($format === 'EXCEL') {
                    return $this->exportUtil->exportVisitorsToExcel($visitorsList);
                } elseif ($format === 'PDF') {
                    return $this->exportUtil->exportVisitorsListToPDF($visitorsList);
                }

                // redirect back to export page
                return $this->redirectToRoute('admin_visitor_manager_download');
            }
        }

        // return visitors data export form
        return $this->render('admin/element/form/visitors-export-form.twig', [
            'form' => $form->createView(),
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * Display the visitors metrics page
     *
     * @return Response The visitors metrics page view
     */
    #[Route('/admin/visitors/metrics', methods: ['GET', 'POST'], name: 'admin_visitor_manager_metrics')]
    public function visitorsMetrics(): Response
    {
        //dd($this->visitorManager->getVisitorMetrics('last_week'));

        return new Response('test');
    }
}
