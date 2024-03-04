<?php

namespace App\EventSubscriber;

use App\Util\SecurityUtil;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormSubmitSubscriber
 *
 * Event subscriber for processing form submissions.
 * 
 * @see EventSubscriberInterface
 * 
 * @package App\EventSubscriber
 */
class FormSubmitSubscriber implements EventSubscriberInterface
{
    /**
     * @var SecurityUtil
     * Instance of the SecurityUtil for handling security-related utilities.
     */
    private SecurityUtil $securityUtil;

    /**
     * VisitorManagerController constructor.
     *
     * @param SecurityUtil    $securityUtil
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Subscribes to the request event.
     *
     * @return array<string> The subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * Handles the kernel request event for escape all values in post requests.
     *
     * @param RequestEvent $event The request event.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // check if request is post
        if ($request->isMethod('POST')) {

            // get from data
            $form_data = $request->request->all();

            // escape all inputs
            array_walk_recursive($form_data, function (&$value) {
                $value = $this->securityUtil->escapeString($value);
            });

            // return escaped from data
            $request->request->replace($form_data);
        }
    }
}
