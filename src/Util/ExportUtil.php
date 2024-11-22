<?php

namespace App\Util;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExportUtil
 *
 * ExportUtil provides methods for exporting visitor data from database
 *
 * @package App\Util
 */
class ExportUtil
{
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(VisitorInfoUtil $visitorInfoUtil)
    {
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Export visitors list data to excel file
     *
     * @param array<mixed> $dataToExport The visitors list data
     *
     * @return Response The Excel file response
     */
    public function exportVisitorsToExcel(array $dataToExport): Response
    {
        // create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // set the headers for the Excel sheet
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
        foreach ($dataToExport as $visitor) {
            $sheet->setCellValue('A' . $row, $visitor->getId());
            $sheet->setCellValue('B' . $row, $visitor->getFirstVisit()->format('Y-m-d H:i:s'));
            $sheet->setCellValue('C' . $row, $visitor->getLastVisit()->format('Y-m-d H:i:s'));
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
                'startColor' => ['argb' => 'FF1F1F1F'], // dark background for entire sheet
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
     * @param array<mixed> $dataToExport The visitors list data
     *
     * @return Response The PDF file response
     */
    public function exportVisitorsListToPDF(array $dataToExport): Response
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

        // table columns list
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

        // add visitor to table
        foreach ($dataToExport as $visitor) {
            $html .= '<tr>';
            $html .= '<td>' . $visitor->getId() . '</td>';
            $html .= '<td>' . $visitor->getFirstVisit()->format('Y-m-d H:i:s') . '</td>';
            $html .= '<td>' . $visitor->getLastVisit()->format('Y-m-d H:i:s') . '</td>';
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
