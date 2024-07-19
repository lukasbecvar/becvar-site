<?php

namespace App\Util;

use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SiteUtil
 *
 * SiteUtil provides basic site-related methods
 *
 * @package App\Util
 */
class SiteUtil
{
    private CacheUtil $cacheUtil;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;

    public function __construct(CacheUtil $cacheUtil, ErrorManager $errorManager, SecurityUtil $securityUtil)
    {
        $this->cacheUtil = $cacheUtil;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
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
     * Get the host server ip address
     *
     * @return string The host server ip address
     */
    public function getHostServerIpAddress(): string
    {
        $IpInfoApiUrl = $this->isSsl() ? 'https://' . $this->getHttpHost() . '/api/ipnfo' : 'http://' . $this->getHttpHost() . '/api/ipnfo';

        // check if host server ip address cached
        if ($this->cacheUtil->isCatched('host_server_ip_address')) {
            // return cached host server ip address
            return $this->cacheUtil->getValue('host_server_ip_address')->get();
        }

        // get host server ip address
        try {
            $hostServerIpAddress = file_get_contents(
                $IpInfoApiUrl,
                context: stream_context_create(
                    ['http' => ['timeout' => 3]
                    ]
                )
            );
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                msg: 'error to get host server ip address: ' . $e->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
            $hostServerIpAddress = '127.0.0.1';
        }

        // cache host server ip address
        $this->cacheUtil->setValue('host_server_ip_address', $hostServerIpAddress, 86400);

        // return host server ip address
        return $hostServerIpAddress;
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
