<?php

namespace App\Util;

use Detection\MobileDetect;

/*
    Visitor info util provides visitors info getters
*/

class VisitorInfoUtil
{
    private SiteUtil $siteUtil;
    private JsonUtil $jsonUtil;

    public function __construct(SiteUtil $siteUtil, JsonUtil $jsonUtil)
    {
        $this->siteUtil = $siteUtil;
        $this->jsonUtil = $jsonUtil;
    }

    public function getIP(): ?string 
    {
        // check client ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];

        // check forwarded ip (get ip from cloudflare visitors) 
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {

            // default header
            $address = $_SERVER['REMOTE_ADDR'];
        }
        return $address;
    }

    public function getBrowser(): ?string 
    {
        // get user agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        // check if user agent found
        if ($user_agent != null) {
            return $user_agent;
        } else {
            return 'Unknown';
        }
    }

    public function getBrowserShortify(string $user_agent): ?string 
    {
        $output = null;

        // identify shortify array [ID: str_contains, Value: replacement]
        $browser_list = $this->jsonUtil->getJson(__DIR__.'/../../browser-list.json');

        // check if browser list found
        if ($browser_list != null) {

            // check all user agents 
            foreach ($browser_list as $index => $value) {

                // check if index found in agent
                if (str_contains($user_agent, $index)) {
                    $output = $value;
                }
            }
        }

        // check if output is not found in browser list
        if ($output == null) {

            // identify Internet explorer
            if(preg_match('/MSIE (\d+\.\d+);/', $user_agent)) {
                $output = 'Internet Explore';

            } else if (str_contains($user_agent, 'MSIE')) {
                $output = 'Internet Explore';   

            // identify Google chrome
            } else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $user_agent) ) {
                $output = 'Chrome';
            
            // identify Internet edge
            } else if (preg_match('/Edge\/\d+/', $user_agent)) {
                $output = 'Edge';
            
            // identify Firefox
            } else if (preg_match('/Firefox[\/\s](\d+\.\d+)/', $user_agent)) {
                $output = 'Firefox';

            } else if (str_contains($user_agent, 'Firefox/96')) {
                $output = 'Firefox/96';  
                
            // identify Safari
            } else if (preg_match('/Safari[\/\s](\d+\.\d+)/', $user_agent)) {
                $output = 'Safari';
                
            // identify UC Browser
            } else if (str_contains($user_agent, 'UCWEB')) {
                $output = 'UC Browser';

            // identify UCBrowser Browser
            } else if (str_contains($user_agent, 'UCBrowser')) {
                $output = 'UC Browser';

            // identify IceApe Browser
            } else if (str_contains($user_agent, 'Iceape')) {
                $output = 'IceApe Browser';

            // identify Maxthon Browser
            } else if (str_contains($user_agent, 'maxthon')) {
                $output = 'Maxthon Browser';

            // identify Konqueror Browser
            } else if (str_contains($user_agent, 'konqueror')) {
                $output = 'Konqueror Browser';

            // identify NetFront Browser
            } else if (str_contains($user_agent, 'NetFront')) {
                $output = 'NetFront Browser';

            // identify Midori Browser
            } else if (str_contains($user_agent, 'Midori')) {
                $output = 'Midori Browser';

            // identify Opera
            } else if (preg_match('/OPR[\/\s](\d+\.\d+)/', $user_agent)) {
                $output = 'Opera';

            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $user_agent)) {
                $output = 'Opera';
            }
        }

        // if not found
        if ($output == null) {
            $output = $user_agent;
        
        } 

        return $output;
    }

    public function getOS(): ?string 
    { 
        // get user agent
        $agent = $this->getBrowser();
                
        // OS list
        $os_array = array (
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
        
        // check all os in list
        foreach ($os_array as $index => $value) {

            if ($index != null && $agent != null) {
                
                // check if os found
                if (preg_match($index, $agent)) {
                    return $value;
                } else {
                    return 'Unknown OS';
                }
            } else {
                return 'Unknown OS';
            }
        }
    }

    public function getLocation(string $ip_address): ?array
    {
        // check if site running on localhost
        if ($this->siteUtil->isRunningLocalhost()) {
            return ['city' => 'locale', 'country' => 'host'];
        } else {
 
            try {
                // get geoplugin url form app enviroment
                $geoplugin_url = $_ENV['GEOPLUGIN_URL'];

                // get data from geoplugin
                $geoplugin_data = file_get_contents($geoplugin_url.'/json.gp?ip='.$ip_address);

                // decode data
                $details = json_decode($geoplugin_data);
        
                // get country
                $country = $details->geoplugin_countryCode;

                // check if city name defined
                if (!empty(explode('/', $details->geoplugin_timezone)[1])) {
                        
                    // get city name from timezone (explode /)
                    $city = explode('/', $details->geoplugin_timezone)[1];
                } else {
                    $city = null;
                }

            } catch (\Exception) {

                // set null if data not getted
                $country = null;
                $city = null;
            }   
        }

        // empty set to null
        if (empty($country)) {
            $country = 'Unknown';
        }
        if (empty($city)) {
            $city = 'Unknown';
        }

        return ['city' => $city, 'country' => $country];
    }

    public function isMobile(): bool 
    {
        $detect = new MobileDetect();
        return $detect->isMobile();
    }
}
