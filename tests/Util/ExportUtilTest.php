<?php

namespace App\Tests\Util;

use DateTime;
use App\Entity\Visitor;
use App\Util\ExportUtil;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExportUtilTest
 *
 * Test cases for data export utils
 *
 * @package App\Tests\Util
 */
class ExportUtilTest extends TestCase
{
    private VisitorInfoUtil & MockObject $visitorInfoUtilMock;
    private ExportUtil $exportUtil;

    protected function setUp(): void
    {
        // mock for VisitorInfoUtil
        $this->visitorInfoUtilMock = $this->createMock(VisitorInfoUtil::class);

        // init export util instance
        $this->exportUtil = new ExportUtil($this->visitorInfoUtilMock);
    }

    /**
     * Test for export visitors to excel
     *
     * @return void
     */
    public function testExportVisitorsToExcel(): void
    {
        // mock visitor entity
        $visitorMock = $this->createMock(Visitor::class);
        $visitorMock->method('getId')->willReturn(1);
        $visitorMock->method('getFirstVisit')->willReturn(new DateTime('2023-12-01'));
        $visitorMock->method('getLastVisit')->willReturn(new DateTime('2023-12-01'));
        $visitorMock->method('getBrowser')->willReturn('Firefox');
        $visitorMock->method('getOs')->willReturn('Linux');
        $visitorMock->method('getCity')->willReturn('Prague');
        $visitorMock->method('getCountry')->willReturn('Czech Republic');
        $visitorMock->method('getIpAddress')->willReturn('192.168.0.1');

        // mock metody getBrowserShortify
        $this->visitorInfoUtilMock->method('getBrowserShortify')->with('Firefox')->willReturn('FF');

        // mock export data
        $dataToExport = [$visitorMock];

        // call export visitors to excel
        $response = $this->exportUtil->exportVisitorsToExcel($dataToExport);

        // check response is instance of Response
        $this->assertInstanceOf(Response::class, $response);

        // check response headers
        $this->assertEquals(
            expected: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            actual: $response->headers->get('Content-Type')
        );
        $this->assertStringContainsString('attachment; filename="visitors_list_', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        // check if output is not empty
        $this->assertNotEmpty($output);
    }

    /**
     * Test for export visitors list to PDF
     *
     * @return void
     */
    public function testExportVisitorsListToPDF(): void
    {
        // mock visitor entity
        $visitorMock = $this->createMock(Visitor::class);
        $visitorMock->method('getId')->willReturn(1);
        $visitorMock->method('getFirstVisit')->willReturn(new DateTime('2023-12-01'));
        $visitorMock->method('getLastVisit')->willReturn(new DateTime('2023-12-01'));
        $visitorMock->method('getBrowser')->willReturn('Firefox');
        $visitorMock->method('getOs')->willReturn('Linux');
        $visitorMock->method('getCity')->willReturn('Prague');
        $visitorMock->method('getCountry')->willReturn('Czech Republic');
        $visitorMock->method('getIpAddress')->willReturn('192.168.0.1');

        // mock metody getBrowserShortify
        $this->visitorInfoUtilMock->method('getBrowserShortify')->with('Firefox')->willReturn('FF');

        // mock export data
        $dataToExport = [$visitorMock];

        // call export method
        $response = $this->exportUtil->exportVisitorsListToPDF($dataToExport);

        // check if response is instance of Response
        $this->assertInstanceOf(Response::class, $response);

        // check response headers
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="visitors-list_', $response->headers->get('Content-Disposition'));

        // check response data
        $output = $response->getContent();
        $this->assertNotEmpty($output);
    }
}
