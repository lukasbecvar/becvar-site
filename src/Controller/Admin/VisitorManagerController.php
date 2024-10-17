<?php

namespace App\Controller\Admin;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Util\AppUtil;
use App\Entity\Visitor;
use App\Form\BanFormType;
use App\Manager\BanManager;
use App\Manager\AuthManager;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use App\Form\VisitorListExportType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
    private BanManager $banManager;
    private AuthManager $authManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(
        AppUtil $appUtil,
        BanManager $banManager,
        AuthManager $authManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil
    ) {
        $this->appUtil = $appUtil;
        $this->banManager = $banManager;
        $this->authManager = $authManager;
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

            // check if error found
            if ($errorMsg == null) {
                // get visitors list
                $visitorsList = $this->visitorManager->getVisitorsByFilter($filter);

                // export data with valid method
                if ($format === 'EXCEL') {
                    return $this->exportVisitorsListToExcel($visitorsList);
                } elseif ($format === 'PDF') {
                    return $this->exportVisitorsListToPDF($visitorsList);
                }

                // redirect back to export page
                return $this->redirectToRoute('admin_visitor_manager_download');
            }
        }

        return $this->render('admin/elements/forms/visitors-export-form.twig', [
            // user data
            'userName' => $this->authManager->getUsername(),
            'userRole' => $this->authManager->getUserRole(),
            'userPic' => $this->authManager->getUserProfilePic(),

            'form' => $form->createView(),
            'errorMsg' => $errorMsg
        ]);
    }

    /**
     * Export visitors list data to Excel file
     *
     * @param array<mixed> $visitorsList The visitors list data
     *
     * @return Response The Excel file response
     */
    private function exportVisitorsListToExcel(array $visitorsList): Response
    {
        // create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // set the headers for the Excel sheet with dark theme styling
        $headers = ['ID', 'First Visit', 'Last Visit', 'Browser', 'OS', 'City', 'Country', 'IP Address'];
        $sheet->fromArray($headers, null, 'A1');

        // apply styles to the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // white text
                'size' => 12, // larger font size
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F1F1F'], // dark background
            ],
        ];

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // populate the Excel sheet with data
        $row = 2; // start from the second row (under header row)
        foreach ($visitorsList as $visitor) {
            $sheet->setCellValue('A' . $row, $visitor->getId());
            $sheet->setCellValue('B' . $row, $visitor->getFirstVisit());
            $sheet->setCellValue('C' . $row, $visitor->getLastVisit());
            $sheet->setCellValue('D' . $row, $this->visitorInfoUtil->getBrowserShortify($visitor->getBrowser()));
            $sheet->setCellValue('E' . $row, $visitor->getOs());
            $sheet->setCellValue('F' . $row, $visitor->getCity());
            $sheet->setCellValue('G' . $row, $visitor->getCountry());

            // set the IP address with yellow color
            $ipAddressCell = 'H' . $row;
            $sheet->setCellValue($ipAddressCell, $visitor->getIpAddress());

            // apply yellow color style to the IP address cell
            $sheet->getStyle($ipAddressCell)->applyFromArray([
                'font' => [
                    'color' => ['argb' => 'FFFF00'], // yellow color for IP address
                    'size' => 12, // larger font size
                ],
            ]);

            $row++;
        }

        // apply styling to the data rows
        $dataStyle = [
            'font' => [
                'color' => ['argb' => 'FFFFFFFF'], // white text
                'size' => 12, // larger font size
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E1E1E'], // darker background for data rows
            ],
        ];

        // set style for data rows
        $sheet->getStyle('A2:H' . ($row - 1))->applyFromArray($dataStyle);

        // set column widths based on header and data
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // automatically adjust column width
        }

        // center the ID column (column A)
        $sheet->getStyle('A2:A' . ($row - 1))->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // add borders to all cells in the table
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF808080'], // gray border color
                ],
            ],
        ];

        // apply the border style to the header and data rows
        $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray($borderStyle);

        // set the background color of the entire sheet to dark
        $spreadsheet->getActiveSheet()->getStyle('A1:H' . ($row - 1))->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F1F1F'], // sark background for entire sheet
            ],
        ]);

        // create a new Xlsx writer
        $writer = new Xlsx($spreadsheet);

        // start output buffering
        ob_start();

        // save the spreadsheet to php://output
        $writer->save('php://output');

        // get the contents of the buffer
        $output = ob_get_clean();

        // prepare the response
        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="visitors_list_' . date('Y-m-d') . '.xlsx"');

        return $response;
    }

    /**
     * Export visitors list data to PDF file
     *
     * @param array<mixed> $visitorsList The visitors list data
     *
     * @return Response The PDF file response
     */
    private function exportVisitorsListToPDF(array $visitorsList): Response
    {
        // initialize Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // prepare HTML content for PDF with dark theme styling
        $html = '
        <style>
            @page {
                margin: 0px; /* Remove page margins */
            }
            body {
                margin: 0px; /* Remove body margins */
                padding: 0px;
                background-color: #121212;
                color: #E0E0E0;
                font-family: Arial, sans-serif;
            }
            h1 {
                text-align: center;
                color: ;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #E0E0E0;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #1F1F1F;
                color: #BB86FC;
            }
            tr:nth-child(even) {
                background-color: #2C2C2C;
            }
            tr:nth-child(odd) {
                background-color: #1E1E1E;
            }
        </style>';

        $html .= '<h1>Visitors List</h1>';
        $html .= '<table>';
        $html .= '<tr>
            <th>ID</th>
            <th>First Visit</th>
            <th>Last Visit</th>
            <th>Browser</th>
            <th>OS</th>
            <th>City</th>
            <th>Country</th>
            <th>IP Address</th>
        </tr>';

        foreach ($visitorsList as $visitor) {
            $html .= '<tr>';
            $html .= '<td>' . $visitor->getId() . '</td>';
            $html .= '<td>' . $visitor->getFirstVisit() . '</td>';
            $html .= '<td>' . $visitor->getLastVisit() . '</td>';
            $html .= '<td>' . $this->visitorInfoUtil->getBrowserShortify($visitor->getBrowser()) . '</td>';
            $html .= '<td>' . $visitor->getOs() . '</td>';
            $html .= '<td>' . $visitor->getCity() . '</td>';
            $html .= '<td>' . $visitor->getCountry() . '</td>';
            $html .= '<td>' . $visitor->getIpAddress() . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        // load HTML content to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // prepare the response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="visitors-list_' . date('Y-m-d') . '.pdf"');
        $response->setContent($dompdf->output());

        return $response;
    }
}
