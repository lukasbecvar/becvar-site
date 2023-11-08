<?php

namespace App\Util;

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
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $address = $_SERVER['REMOTE_ADDR'];
        }
        return $address;
    }

    public function getBrowser(): ?string 
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'Unknown';

        if ($user_agent != null) {
            $browser = $user_agent;
        }
            
        return $browser;
    }

    public function getBrowserShortify(string $user_agent): ?string 
    {
        $out = null;

        // identify shortify array [ID: str_contains, Value: replacement]
        $browser_list = $this->jsonUtil->getJson(__DIR__.'/../../browser-list.json');

        // check if browser list found
        if ($browser_list != NULL) {

            // get short output from browser list
            foreach ($browser_list as $index => $value) {
                if (str_contains($user_agent, $index)) {
                    $out = $value;
                }
            }
        }

        if ($out == null) {
            // identify Internet explorer
            if(preg_match('/MSIE (\d+\.\d+);/', $user_agent)) {
                $out = 'Internet Explore';

            } else if (str_contains($user_agent, 'MSIE')) {
                $out = 'Internet Explore';   

            // identify Google chrome
            } else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $user_agent) ) {
                $out = 'Chrome';
            
            // identify Internet edge
            } else if (preg_match('/Edge\/\d+/', $user_agent)) {
                $out = 'Edge';
            
            // identify Firefox
            } else if (preg_match('/Firefox[\/\s](\d+\.\d+)/', $user_agent)) {
                $out = 'Firefox';

            } else if (str_contains($user_agent, 'Firefox/96')) {
                $out = 'Firefox/96';  
                
            // identify Safari
            } else if (preg_match('/Safari[\/\s](\d+\.\d+)/', $user_agent)) {
                $out = 'Safari';
                
            // identify UC Browser
            } else if (str_contains($user_agent, 'UCWEB')) {
                $out = 'UC Browser';

            // identify UCBrowser Browser
            } else if (str_contains($user_agent, 'UCBrowser')) {
                $out = 'UC Browser';

            // identify IceApe Browser
            } else if (str_contains($user_agent, 'Iceape')) {
                $out = 'IceApe Browser';

            // identify Maxthon Browser
            } else if (str_contains($user_agent, 'maxthon')) {
                $out = 'Maxthon Browser';

            // identify Konqueror Browser
            } else if (str_contains($user_agent, 'konqueror')) {
                $out = 'Konqueror Browser';

            // identify NetFront Browser
            } else if (str_contains($user_agent, 'NetFront')) {
                $out = 'NetFront Browser';

            // identify Midori Browser
            } else if (str_contains($user_agent, 'Midori')) {
                $out = 'Midori Browser';

            // identify Opera
            } else if (preg_match('/OPR[\/\s](\d+\.\d+)/', $user_agent)) {
                $out = 'Opera';

            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $user_agent)) {
                $out = 'Opera';
            }
        }

        // if notfound
        if ($out == null) {
            $out = $user_agent;
        }

        return $out;
    }

    public function getOS(): ?string 
    { 
        $agent = $this->getBrowser();
        
        $os = 'Unknown OS';
        
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
        
        foreach ($os_array as $regex => $value) {

            // check if os found
            if ($regex != null && $agent != null) {
                if (preg_match($regex, $agent)) {
                    $os = $value;
                }
            }
        }
        
        return $os;
    }

    public function getLocation(string $ip_address): ?array
    {
        // check if site running on localhost
        if ($this->siteUtil->isRunningLocalhost()) {
            return ['city' => 'locale', 'country' => 'host'];
        } else {
 
            try {
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

}
