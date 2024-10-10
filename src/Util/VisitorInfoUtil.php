<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 *
 * VisitorInfoUtil provides methods to get information about visitors
 *
 * @package App\Util
 */
class VisitorInfoUtil
{
    private AppUtil $appUtil;
    private JsonUtil $jsonUtil;

    public function __construct(AppUtil $appUtil, JsonUtil $jsonUtil)
    {
        $this->appUtil = $appUtil;
        $this->jsonUtil = $jsonUtil;
    }

    /**
     * Get the client's IP address
     *
     * @return string|null The client's IP address
     */
    public function getIP(): ?string
    {
        // check client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check forwarded IP (get IP from cloudflare visitors)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        // default addr get
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get the user agent
     *
     * @return string|null The user agent
     */
    public function getUserAgent(): ?string
    {
        // get user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        /** @var string $browserAgent return user agent */
        $browserAgent = $userAgent !== null ? $userAgent : 'Unknown';

        // escape user agent
        $browserAgent = htmlspecialchars($browserAgent, ENT_QUOTES | ENT_HTML5);

        return $browserAgent;
    }

    /**
     * Get a short version of the browser name
     *
     * @param string $userAgent The user agent string
     *
     * @return string|null The short browser name
     */
    public function getBrowserShortify(string $userAgent = null): ?string
    {
        // set useragent if not set
        if ($userAgent == null) {
            $userAgent = $this->getUserAgent();
        }

        $output = null;

        // identify shortify array [ID: str_contains, Value: replacement]
        $browser_list = $this->jsonUtil->getJson(__DIR__ . '/../../config/browser-list.json');

        // check if browser list found
        if ($browser_list != null) {
            // check all user agents
            foreach ($browser_list as $index => $value) {
                // check if index found in agent
                if (str_contains($userAgent, $index)) {
                    $output = $index;
                    break;
                }
            }
        }

        // check if output is not found in browser list
        if ($output == null) {
            // identify common browsers using switch statement
            switch (true) {
                case preg_match('/MSIE (\d+\.\d+);/', $userAgent):
                case str_contains($userAgent, 'MSIE'):
                    $output = 'Internet Explore';
                    break;
                case preg_match('/Chrome[\/\s](\d+\.\d+)/', $userAgent):
                    $output = 'Chrome';
                    break;
                case preg_match('/Edge\/\d+/', $userAgent):
                    $output = 'Edge';
                    break;
                case preg_match('/Firefox[\/\s](\d+\.\d+)/', $userAgent):
                case str_contains($userAgent, 'Firefox/96'):
                    $output = 'Firefox';
                    break;
                case preg_match('/Safari[\/\s](\d+\.\d+)/', $userAgent):
                    $output = 'Safari';
                    break;
                case str_contains($userAgent, 'UCWEB'):
                case str_contains($userAgent, 'UCBrowser'):
                    $output = 'UC Browser';
                    break;
                case str_contains($userAgent, 'Iceape'):
                    $output = 'IceApe Browser';
                    break;
                case str_contains($userAgent, 'maxthon'):
                    $output = 'Maxthon Browser';
                    break;
                case str_contains($userAgent, 'konqueror'):
                    $output = 'Konqueror Browser';
                    break;
                case str_contains($userAgent, 'NetFront'):
                    $output = 'NetFront Browser';
                    break;
                case str_contains($userAgent, 'Midori'):
                    $output = 'Midori Browser';
                    break;
                case preg_match('/OPR[\/\s](\d+\.\d+)/', $userAgent):
                case preg_match('/Opera[\/\s](\d+\.\d+)/', $userAgent):
                    $output = 'Opera';
                    break;
                default:
                    // if not found, check user agent length
                    if (str_contains($userAgent, ' ') || strlen($userAgent) >= 39) {
                        $output = 'Unknown';
                    } else {
                        $output = $userAgent;
                    }
            }
        }

        return $output;
    }

    /**
     * Get the operating system
     *
     * @return string|null The operating system
     */
    public function getOS(): ?string
    {
        $os = 'Unknown OS';

        // get browser agent
        $agent = $this->getUserAgent();

        // OS list
        $osArray = array (
            '/windows nt 5.2/i'     =>  'Windows Server_2003',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/win16/i'              =>  'Windows 3.11',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/blackberry/i'         =>  'BlackBerry',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/SMART-TV/i'           =>  'Smart TV',
            '/windows/i'            =>  'Windows',
            '/iphone/i'             =>  'Mac IOS',
            '/android/i'            =>  'Android',
            '/webos/i'              =>  'Mobile',
            '/ubuntu/i'             =>  'Ubuntu',
            '/linux/i'              =>  'Linux',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad'
        );

        // find os
        foreach ($osArray as $regex => $value) {
            // check if os found
            if ($regex != null && $agent != null) {
                if (preg_match($regex, $agent)) {
                    $os = $value;
                }
            }
        }

        return $os;
    }

    /**
     * Retrieves information about an IP address using a geolocation API
     *
     * @param string $ipAddress The IP address to look up
     *
     * @return object|null The decoded JSON response from the geolocation API, or null if an error occurs
     */
    public function getIpInfo(string $ipAddress): ?object
    {
        // create stream context with timeout of 1 second
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 3
            )
        ));

        try {
            // get response
            $response = file_get_contents(
                $_ENV['GEOLOCATION_API_URL'] . '/json/' . $ipAddress,
                false,
                $context
            );

            // decode response & return data
            return json_decode($response);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Retrieves the location (city and country) for a given IP address
     *
     * @param string $ipAddress The IP address to look up
     *
     * @return array<string>|null An associative array containing the city and country, or null if an error occurs
     */
    public function getLocation(string $ipAddress): ?array
    {
        // check if site is running on localhost
        if ($this->appUtil->isRunningLocalhost()) {
            return ['city' => 'locale', 'country' => 'host'];
        }

        try {
            // decode response
            $data = $this->getIpInfo($ipAddress);

            // check if country code seted
            if (isset($data->countryCode)) {
                $country = $data->countryCode;
            } else {
                $country = 'Unknown';
            }

            // check if city seted
            if (isset($data->city)) {
                $city = $data->city;
            } else {
                $city = 'Unknown';
            }

            // return data
            return ['city' => $city, 'country' => $country];
        } catch (\Exception $e) {
            return ['city' => 'Unknown', 'country' => 'Unknown'];
        }
    }
}
