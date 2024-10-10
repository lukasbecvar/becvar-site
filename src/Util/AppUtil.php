<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppUtil
 *
 * AppUtil provides basic site-related methods
 *
 * @package App\Util
 */
class AppUtil
{
    private SecurityUtil $securityUtil;
    private KernelInterface $kernelInterface;

    public function __construct(SecurityUtil $securityUtil, KernelInterface $kernelInterface)
    {
        $this->securityUtil = $securityUtil;
        $this->kernelInterface = $kernelInterface;
    }

    /** Get the application root directory
     *
     * @return string The application root directory
     */
    public function getAppRootDir(): string
    {
        return $this->kernelInterface->getProjectDir();
    }

    /**
     * Get the HTTP host
     *
     * @return string|null The HTTP host
     */
    public function getHttpHost(): ?string
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Check if the application is running on localhost
     *
     * @return bool Whether the application is running on localhost
     */
    public function isRunningLocalhost(): bool
    {
        $localhost = false;

        // get host url
        $host = $this->getHttpHost();

        // check if output is string value
        if (is_string($host)) {
            // check if running on url localhost
            if (str_starts_with($host, 'localhost')) {
                $localhost = true;
            }

            // check if running on localhost ip
            if (str_starts_with($host, '127.0.0.1')) {
                $localhost = true;
            }

            // check if running on private ip
            if (str_starts_with($host, '10.0.0.93')) {
                $localhost = true;
            }
        }

        return $localhost;
    }

    /**
     * Check if the connection is secure (SSL)
     *
     * @return bool Whether the connection is secure
     */
    public function isSsl(): bool
    {
        // check if HTTPS header is set and its value is either 1 or 'on'
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) === 'on');
    }

    /**
     * Check if the application is in maintenance mode
     *
     * @return bool Whether the application is in maintenance mode
     */
    public function isMaintenance(): bool
    {
        return $_ENV['MAINTENANCE_MODE'] === 'true';
    }

    /**
     * Check if the ssl only mode
     *
     * @return bool Whether the application is under ssl only mode
     */
    public function isSSLOnly(): bool
    {
        return $_ENV['SSL_ONLY'] === 'true';
    }

    /**
     * Check if the application is in development mode
     *
     * @return bool Whether the application is in development mode
     */
    public function isDevMode(): bool
    {
        if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test') {
            return true;
        }

        return false;
    }

    /**
     * Get the value of a query string parameter, with XSS protection
     *
     * @param string $query The query string parameter name
     * @param Request $request The Symfony request object
     *
     * @return string|null The sanitized value of the query string parameter
     */
    public function getQueryString(string $query, Request $request): ?string
    {
        // get query value
        $value = $request->query->get($query);

        if ($value == null) {
            return '1';
        } else {
            // escape query string value (XSS Protection)
            return $this->securityUtil->escapeString($value);
        }
    }
}
