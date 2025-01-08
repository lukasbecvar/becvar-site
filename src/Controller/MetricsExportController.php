<?php

namespace App\Controller;

use App\Util\AppUtil;
use App\Util\VisitorInfoUtil;
use App\Manager\VisitorManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class MetricsExportController
 *
 * Controller for exporting paste metrics
 *
 * @package App\Controller
 */
class MetricsExportController extends AbstractController
{
    private AppUtil $appUtil;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(AppUtil $appUtil, VisitorManager $visitorManager, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->appUtil = $appUtil;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Export paste metrics
     *
     * @return JsonResponse The paste metrics
     */
    #[Route('/metrics/export', methods: ['GET'], name: 'metrics_export')]
    public function exportMetrics(): JsonResponse
    {
        // check if metrics exporter is enabled
        if ($this->appUtil->getEnvValue('METRICS_EXPORTER_ENABLED') != 'true') {
            return $this->json(['error' => 'Metrics exporter is not enabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // check if visitor ip is allowed to access metrics
        if ($this->visitorInfoUtil->getIP() !== $this->appUtil->getEnvValue('METRICS_EXPORTER_ALLOWED_IP')) {
            return $this->json(['error' => 'Your IP is not allowed to access metrics.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // get visitor metrics
        $visitorMetrics = $this->visitorManager->getVisitorMetrics('last_24_hours');

        // return metrics data
        return $this->json([
            'visitors_cities' => $visitorMetrics['visitorsCity'],
            'visitors_count' => $visitorMetrics['visitorsCount'],
            'visitors_country' => $visitorMetrics['visitorsCountry'],
            'visitors_browsers' => $visitorMetrics['visitorsBrowsers']
        ], JsonResponse::HTTP_OK);
    }
}
