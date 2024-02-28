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
    /**
     * @var AuthManager
     * Instance of the AuthManager for handling authentication-related functionality.
     */
    private AuthManager $authManager;

    /**
     * @var UrlGeneratorInterface
     * Instance of the UrlGeneratorInterface for login redirect functionality.
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * AuthentificatedCheckMiddleware constructor.
     *
     * @param AuthManager           $authManager
     * @param UrlGeneratorInterface $urlGenerator
     */
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
                // redirect to login page
                $loginUrl = $this->urlGenerator->generate('auth_login'); 
                $event->setResponse(new RedirectResponse($loginUrl));
            }
        }
    }
}
