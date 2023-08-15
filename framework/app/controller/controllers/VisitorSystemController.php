<?php // visitor system controller & manager

    namespace becwork\controllers;

    class VisitorSystemController {

        // get user browser
        public function getBrowser() {

            // get user agent
            $agent = $_SERVER["HTTP_USER_AGENT"];

            // return Unknown
            if ($agent == null) {
                $browser = "Unknown";
           
            // return browser agent
            } else {
                $browser = $agent;
            }
            
            // return browser ID
            return $browser;
        }

        // shortify BrowserID
        public function getShortBrowserID($raw) {
            
            global $browsersList;

            // init default value
            $out = $raw;

            // default found in browser list
            $found = "no";

            // identify Internet explorer
            if(preg_match('/MSIE (\d+\.\d+);/', $raw)) {
                $out = "Internet Explore";
                $found = "yes";

            } else if (str_contains($raw, 'MSIE')) {
                $out = "Internet Explore";   
                $found = "yes"; 

            // identify Google chrome
            } else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Chrome";
                $found = "yes";
            
            // identify Internet edge
            } else if (preg_match('/Edge\/\d+/', $raw)) {
                $out = "Edge";
                $found = "yes";
            
            // identify Firefox
            } else if (preg_match('/Firefox[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Firefox";
                $found = "yes";

            } else if (str_contains($raw, 'Firefox/96')) {
                $out = "Firefox/96";  
                $found = "yes";          
                
            // identify Safari
            } else if (preg_match('/Safari[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Safari";
                $found = "yes";
                
            // identify UC Browser
            } else if (str_contains($raw, 'UCWEB')) {
                $out = "UC Browser";
                $found = "yes";
  
            // identify UCBrowser Browser
            } else if (str_contains($raw, 'UCBrowser')) {
                $out = "UC Browser";
                $found = "yes";

            // identify IceApe Browser
            } else if (str_contains($raw, 'Iceape')) {
                $out = "IceApe Browser";
                $found = "yes";

            // identify Maxthon Browser
            } else if (str_contains($raw, 'maxthon')) {
                $out = "Maxthon Browser";
                $found = "yes";

            // identify Konqueror Browser
            } else if (str_contains($raw, 'konqueror')) {
                $out = "Konqueror Browser";
                $found = "yes";

            // identify NetFront Browser
            } else if (str_contains($raw, 'NetFront')) {
                $out = "NetFront Browser";
                $found = "yes";

            // identify Midori Browser
            } else if (str_contains($raw, 'Midori')) {
                $out = "Midori Browser";
                $found = "yes";

            // identify Netscape Navigator
            } else if (str_contains($raw, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9a3pre) Gecko/20070330') ) {
                $out = "Netscape Navigator";
                $found = "yes";

            // identify Opera
            } else if (preg_match('/OPR[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Opera";
                $found = "yes";

            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Opera";
                $found = "yes";
            }

            // identify shortify array [ID: str_contains, Value: replacement]
            $browser_array = $browsersList->browserList;

            // check if browser ID not found
            if ($found == "no") {

                // get short output from browser list
                foreach ($browser_array as $index => $value) {
                    if (str_contains($raw, $index)) {
                        $out = $value;
                        $found = "yes";
                    }
                }
            }

            // non complete agents
            if ($found == "no") {
                if ($raw == "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML") {
                    $out = "Unknown";
                } else if ($raw = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko)") {
                    $out = "Unknown";
                }
            }

            // return output
            return $out;
        }

        // get visitor OS
        public function getVisitorOS() { 

            // get user agent
            $agent = $this->getBrowser();
        
            // define default OS
            $os_platform  = "Unknown OS";
        
            // OS array
            $os_array = array (
                '/windows/i'            =>  'Windows',
                '/windows nt 10/i'      =>  'Windows 10',
                '/windows nt 6.3/i'     =>  'Windows 8.1',
                '/windows nt 6.2/i'     =>  'Windows 8',
                '/windows nt 6.1/i'     =>  'Windows 7',
                '/windows nt 6.0/i'     =>  'Windows Vista',
                '/windows nt 5.2/i'     =>  'Windows Server_2003',
                '/windows nt 5.1/i'     =>  'Windows XP',
                '/windows xp/i'         =>  'Windows XP',
                '/windows nt 5.0/i'     =>  'Windows 2000',
                '/windows me/i'         =>  'Windows ME',
                '/win98/i'              =>  'Windows 98',
                '/win95/i'              =>  'Windows 95',
                '/win16/i'              =>  'Windows 3.11',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile',
                '/SMART-TV/i'           =>  'Smart TV'
            );
        
            // get os name from list
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $agent)) {
                    $os_platform = $value;
                }
            }
        
            // return on
            return $os_platform;
        }

        // first visit site
        public function firstVisit() {
            
            global $mysqlUtils;
            global $mainUtils;
            global $siteController;
            global $escapeUtils;

            // get data
            $visited_sites = 1;
            $first_visit = $escapeUtils->specialCharshStrip(date('d.m.Y H:i'));
            $last_visit = $escapeUtils->specialCharshStrip(date('d.m.Y H:i'));
            $browser = $escapeUtils->specialCharshStrip($this->getBrowser());
            $ip_adress = $escapeUtils->specialCharshStrip($mainUtils->getRemoteAdress());
            $location = $escapeUtils->specialCharshStrip($this->getVisitorLocation($ip_adress));
            $os = $escapeUtils->specialCharshStrip($this->getVisitorOS());

            // check if ip is banned in database
            if ($this->isVisitorBanned($ip_adress)) {
                $banned = "yes";
            } else {
                $banned = "no";
            }

            // save firt visit
            $mysqlUtils->insertQuery("INSERT INTO `visitors`(`visited_sites`, `first_visit`, `last_visit`, `browser`, `os`, `location`, `ip_adress`) VALUES ('$visited_sites', '$first_visit', '$last_visit', '$browser', '$os', '$location', '$ip_adress')");   

            // redirect banned users to banned page
            if ($this->isVisitorBanned($ip_adress)) {

                // log trying to access site if user banned
                $mysqlUtils->logToMysql("Banned", "Banned user with ip: ".$ip_adress." trying to access site");

                // redirect to banned page
                $siteController->redirectError("banned");
            }
        }

        // visit site
        public function visitSite() {

            global $mysqlUtils;
            global $dashboardController;
            global $mainUtils;
            global $siteController;
            global $escapeUtils;

            // get visitor ip
            $ip_adress = $escapeUtils->specialCharshStrip($mainUtils->getRemoteAdress());

            // check if visitors count is zero
            if ($dashboardController->getVisitorsCount() == "0") {
                $this->firstVisit();
            } else {

                // check if visitor exist in table
                if ($this->ifVisitorIsInTable($ip_adress)) {

                    // get key count in db for duplicity check
                    $ip_ids = $mysqlUtils->fetch("SELECT id FROM visitors WHERE `ip_adress`='$ip_adress'");

                    // check if key is not exist in database
                    if (count($ip_ids) == 0) {
                        $this->firstVisit();

                    } else {
                        // get data from mysql by IP
                        $visited_sites = intval($mysqlUtils->fetchValue("SELECT visited_sites FROM visitors WHERE `ip_adress` = '".$ip_adress."'", "visited_sites"));

                        // new values to insert
                        $visited_sites = $visited_sites + 1;
                        $last_visit = $escapeUtils->specialCharshStrip(date('d.m.Y H:i'));
                        $browser = $escapeUtils->specialCharshStrip($this->getBrowser());
                        $ip_adress = $escapeUtils->specialCharshStrip($mainUtils->getRemoteAdress());
                        $os = $escapeUtils->specialCharshStrip($this->getVisitorOS());

                        // update database
                        $mysqlUtils->insertQuery("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET last_visit = '$last_visit' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET browser = '$browser' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET os = '$os' WHERE `ip_adress` = '$ip_adress'");  

                        // check if ip in database is Unknown
                        if ($this->getVisitorLocationFromDatabase($this->getVisitorIDByIP($ip_adress)) == "Unknown") {

                            // get location 
                            $location = $escapeUtils->specialCharshStrip($this->getVisitorLocation($ip_adress));

                            // insert location
                            $mysqlUtils->insertQuery("UPDATE visitors SET location = '$location' WHERE `ip_adress` = '$ip_adress'");  
                        }

                        // show ban page if IP banned
                        if($this->isVisitorBanned($ip_adress)) {

                            // log trying to access site if user banned
                            $mysqlUtils->logToMysql("Banned", "Banned user with ip: ".$ip_adress." trying to access site");

                            // redirect to banned page
                            $siteController->redirectError("banned");
                        }
                    }
                    
                } else { // init first visit for new visitors
                    $this->firstVisit();
                }
            }
        }

        // check if visitor is banned
        public function isVisitorBanned($ip) {

            global $mysqlUtils;

            // get ip ids
            $ip_ids = $mysqlUtils->fetch("SELECT id FROM banned WHERE `ip_adress`='$ip'");
            
            // check if ip is in database
            if (count($ip_ids) > 0) {

                // get banned status
                $banned_status = $mysqlUtils->fetchValue("SELECT status FROM banned WHERE `ip_adress` = '".$ip."'", "status");

                // check if banned status = yes
                if ($banned_status == "banned") {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // get visitor location from table
        public function getVisitorLocationFromDatabase($id) {

            global $mysqlUtils;

            // get visitor data by id
            $visitor = $mysqlUtils->fetch("SELECT * FROM visitors WHERE `id` = '$id'");

            if (count($visitor) > 0) {

                $out = $mysqlUtils->fetchValue("SELECT location FROM visitors WHERE `id` = '$id'", "location");

            } else {
                $out = "Unknown";
            }

            return $out;
        }

        // get visitor location
        public function getVisitorLocation($ip) {

            global $config;

            // check if site running on localhost
            if (($config->getValue("url") == "localhost") or ($config->getValue("url") == "127.0.0.1") or (str_starts_with($config->getValue("url"), "192.168"))) {
                $country = "HOST";
                $city = "Location";
            
            } else {
 
                // get data by IP from ipinfo API 
                $details = json_decode(file_get_contents($config->getValue("geoplugin_url")."/json.gp?ip=$ip"));
       
                // get country and site from API data
                $country = $details->geoplugin_countryCode;

                // check if city name defined
                if (!empty(explode("/", $details->geoplugin_timezone)[1])) {
                    
                    // get city name from timezone (explode /)
                    $city = explode("/", $details->geoplugin_timezone)[1];
                } else {
                    $city = null;
                }
            }

            // set Unknown if country is empty
            if (empty($country)) {
                $country = null;
            }

            // set Unknown city is empty
            if (empty($city)) {
                $city = null;
            }

            // final return
            if  ($country == null or $city == null) {
                return "Unknown";
            } else {
                return $country."/".$city;
            }
        }

        // get user ip by id
        public function getVisitorIPByID($id) {

            global $mysqlUtils;

            // get IDs
            $ids = $mysqlUtils->fetch("SELECT id FROM visitors WHERE `id`='$id'");

            // check if key found in database
            if (count($ids) == 0) {
                return null;
            } else {

                // get visitor ip by key
                $visitorIP = $mysqlUtils->fetchValue("SELECT ip_adress FROM visitors WHERE `id` = '".$id."'", "ip_adress");

                // return ip
                return $visitorIP;
            }
        }

        // get user ip by ip
        public function getVisitorIDByIP($ip) {

            global $mysqlUtils;

            // get IDs by IP
            $ids = $mysqlUtils->fetch("SELECT id FROM visitors WHERE `ip_adress` = '$ip'");

            // check if key found in database
            if (count($ids) == 0) {
                return null;
            } else {

                // get visitor id by ip
                $visitorID = $mysqlUtils->fetchValue("SELECT id FROM visitors WHERE `ip_adress` = '".$ip."'", "id");

                return $visitorID;
            }
        }

        // ban user by IP
        public function bannVisitorByIP($ip, $reason) {
            
            global $mysqlUtils;

            // get IP count from banned table
            $ids = $mysqlUtils->fetch("SELECT id FROM banned WHERE `ip_adress`='$ip'");

            // check if ip found in banned table
            if (count($ids) > 0) {

                // update ban status
                $mysqlUtils->insertQuery("UPDATE banned SET status = 'banned' WHERE `ip_adress` = '$ip'");

                // update reason
                $mysqlUtils->insertQuery("UPDATE banned SET reason = '$reason' WHERE `ip_adress` = '$ip'");

            } else {
                // default banned status
                $status = "banned";

                // get current date
                $banned_date = date("d.m.Y");

                // insert ban users
                $mysqlUtils->insertQuery("INSERT INTO `banned`(`ip_adress`, `reason`, `banned_date`, `status`) VALUES ('$ip', '$reason', '$banned_date', '$status')");
            }
        }
 
        // un-ban user by IP
        public function unbannVisitorByIP($ip) {
            
            global $mysqlUtils;

            // update ban status
            $mysqlUtils->insertQuery("UPDATE banned SET status = 'unbanned' WHERE `ip_adress` = '$ip'");
        }

        // check if visitor is in table
        public function ifVisitorIsInTable($ip) {

            global $mysqlUtils;

            // get IDs where ip
            $ids = $mysqlUtils->fetch("SELECT id FROM visitors WHERE `ip_adress` = '$ip'");
            
            // check if ip found
            if (count($ids) > 0) {
                return true;
            } else {
                return false;
            }
        }

        // call visit or first visit function
        public function init() {

            global $mysqlUtils;
            global $mainUtils;
            global $config;
            global $siteController;
            global $escapeUtils;

            // get user ip
            $ip_adress = $escapeUtils->specialCharshStrip($mainUtils->getRemoteAdress());

            // check if visitor found in database by IP
            if ($this->ifVisitorIsInTable($ip_adress)) {
                $this->visitSite();
            
            // insert new visitor to database
            } else {
                $this->firstVisit();
            }
        }
    }
?>
