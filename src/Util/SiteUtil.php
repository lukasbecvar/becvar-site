<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Request;

/**
 * SiteUtil provides basic site-related methods.
 */
class SiteUtil
{
    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /**
     * SiteUtil constructor.
     *
     * @param SecurityUtil $securityUtil The SecurityUtil instance.
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Get the HTTP host.
     *
     * @return string The HTTP host.
     */
    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Check if the application is running on localhost.
     *
     * @return bool Whether the application is running on localhost.
     */
    public function isRunningLocalhost(): bool 
    {
		$localhost = false;

        // get host url
        $host = $this->getHttpHost();

        // check if host is null
        if ($host != null) {

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
     * Check if the connection is secure (SSL).
     *
     * @return bool Whether the connection is secure.
     */
    public function isSsl(): bool 
    {
        // check if set https header
        if (isset($_SERVER['HTTPS'])) {

            // https value (1)
            if ($_SERVER['HTTPS'] == 1) {
                return true;

            // check https value (on)
            } elseif ($_SERVER['HTTPS'] == 'on') {
                return true;
            } else {
                return false;   
            }
        } else {
            return false;   
        }
    }

    /**
     * Check if the application is in maintenance mode.
     *
     * @return bool Whether the application is in maintenance mode.
     */
    public function isMaintenance(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the ssl only mode.
     *
     * @return bool Whether the application is under ssl only mode.
     */
    public function isSSLOnly(): bool 
    {
        // check if ssl only mode enabled in app enviroment
        if ($_ENV['SSL_ONLY'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the application is in development mode.
     *
     * @return bool Whether the application is in development mode.
     */
    public function isDevMode(): bool 
    {
        // check if dev mode enabled in app enviroment
        if ($_ENV['APP_ENV'] == 'dev') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the value of a query string parameter, with XSS protection.
     *
     * @param string $query The query string parameter name.
     * @param Request $request The Symfony request object.
     *
     * @return string|null The sanitized value of the query string parameter.
     */
    public function getQueryString(string $query, Request $request): ?string
    {
        // get query value
        $value = $request->query->get($query);

        // set return to 1 if value is null
        if ($value == null) {
            return '1';
        } else {

            // escape query string value (XSS Protection)
            $output = $this->securityUtil->escapeString($value);
            
            // return final output
            return $output;
        }
    }
}
