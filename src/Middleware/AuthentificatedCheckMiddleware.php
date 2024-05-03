<?php

namespace App\Middleware;

use App\Manager\AuthManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AuthentificatedCheckMiddleware
 * 
 * Middleware for checking authentication status before accessing admin routes.
 * 
 * @package App\Middleware
 */
class AuthentificatedCheckMiddleware
{
    private AuthManager $authManager;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(AuthManager $authManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->authManager = $authManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Handles the kernel request event.
     *
     * @param RequestEvent $event The request event.
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path_info = $request->getPathInfo();

        // check if route is admin
        if (str_starts_with($path_info, '/admin')) {
            // check if user is loggedin
            if (!$this->authManager->isUserLogedin()) {
                
                // get login page route url
                $login_url = $this->urlGenerator->generate('auth_login'); 
                
                // redirect to login page
                $event->setResponse(new RedirectResponse($login_url));
            }
        }
    }
}