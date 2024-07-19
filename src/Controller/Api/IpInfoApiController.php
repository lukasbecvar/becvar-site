<?php

namespace App\Controller\Api;

use App\Util\VisitorInfoUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class IpInfoApiController
 *
 * This controller provides API functions for getting visitor ip info
 *
 * @package App\Controller\Api
 */
class IpInfoApiController extends AbstractController
{
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(VisitorInfoUtil $visitorInfoUtil)
    {
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * API endpoint for getting visitor ip info
     *
     * @return Response object representing the HTTP response
     */
    #[Route('/api/ipinfo', methods: ['GET', 'POST'], name: 'api_ipinfo')]
    public function ipInfo(): Response
    {
        $textResponse = new Response($this->visitorInfoUtil->getIP(), 200);
        $textResponse->headers->set('Content-Type', 'text/plain');

        // return response
        return $textResponse;
    }
}
