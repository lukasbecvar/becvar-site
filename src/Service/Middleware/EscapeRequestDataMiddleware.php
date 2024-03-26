<?php

namespace App\Service\Middleware;

use App\Util\SecurityUtil;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class EscapeRequestDataMiddleware
 * 
 * Middleware to escape request data for security purposes.
 * 
 * @package App\Service\Middleware
 */
class EscapeRequestDataMiddleware
{
    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * Constructs the EscapeRequestDataMiddleware.
     *
     * @param SecurityUtil $securityUtil The security utility.
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Handles the kernel request by escaping request data.
     *
     * @param RequestEvent $event The event triggered on kernel request.
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $formData = [];

        // check if request is post or get
        if ($request->isMethod('POST') || $request->isMethod('GET')) {

            // get form data
            if ($request->isMethod('POST')) {
                $formData = $request->request->all();
            } elseif ($request->isMethod('GET')) {
                $formData = $request->query->all();
            }

            // escape all inputs
            array_walk_recursive($formData, function (&$value) {
                $value = $this->securityUtil->escapeString($value);
            });

            // update request data with escaped form data
            if ($request->isMethod('POST')) {
                $request->request->replace($formData);
            } elseif ($request->isMethod('GET')) {
                $request->query->replace($formData);
            }
        }
    }
}
