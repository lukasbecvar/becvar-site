<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Manager\AuthManager;
use Twig\Extension\AbstractExtension;

/**
 * Class AuthManagerExtension
 *
 * Twig extension for the auth manager
 *
 * @package App\Twig
 */
class AuthManagerExtension extends AbstractExtension
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Get the twig functions
     *
     * @return TwigFunction[] An array of TwigFunction objects
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getUserPic', [$this->authManager, 'getUserProfilePic']),
            new TwigFunction('getUsername', [$this->authManager, 'getUsername']),
            new TwigFunction('getUserRole', [$this->authManager, 'getUserRole'])
        ];
    }
}
