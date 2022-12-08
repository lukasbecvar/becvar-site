<?php //Visitor system controller & manager

    class VisitorSystemController {

        //Get user browser
        public function getBrowser() {

            //Get user agent
            $agent = $_SERVER["HTTP_USER_AGENT"];

            //Return undefined
            if ($agent == null) {
                $browser = "Undefined";
           
            //Return browser agent
            } else {
                $browser = $agent;
            }
            
            //Return browser ID
            return $browser;
        }

        //Shortify BrowserID
        public function getShortBrowserID($raw) {
            
            global $browsersList;

            //Init default value
            $out = $raw;

            //Identify Internet explorer
            if(preg_match('/MSIE (\d+\.\d+);/', $raw) ) {
                $out = "Internet Explore";
            } else if (str_contains($raw, 'MSIE') ) {
                $out = "Internet Explore";    

            //Identify Google chrome
            } else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Chrome";
            
            //Identify Internet edge
            } else if (preg_match('/Edge\/\d+/', $raw) ) {
                $out = "Edge";
            
            //Identify Firefox
            } else if (preg_match('/Firefox[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Firefox";
            } else if (str_contains($raw, 'Firefox/96') ) {
                $out = "Firefox/96";            
                
            //Identify Safari
            } else if (preg_match('/Safari[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Safari";
                
            //Identify UC Browser
            } else if (str_contains($raw, 'UCWEB') ) {
                $out = "UC Browser";
  
            //Identify IceApe Browser
            } else if (str_contains($raw, 'Iceape') ) {
                $out = "IceApe Browser";

            //Identify NetFront Browser
            } else if (str_contains($raw, 'NetFront') ) {
                $out = "NetFront Browser";

            //Identify Midori Browser
            } else if (str_contains($raw, 'Midori') ) {
                $out = "Midori Browser";

            //Identify Netscape Navigator
            } else if (str_contains($raw, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9a3pre) Gecko/20070330') ) {
                $out = "Netscape Navigator";

            //Identify Opera
            } else if (preg_match('/OPR[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Opera";
            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $raw) ) {
                $out = "Opera";
            }

            //Non complete agents
            else if ($raw == "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML") {
                $out = "Undefined";
            } else if ($raw = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko)") {
                $out = "Undefined";
            }

            //Identify shortify array [ID: str_contains, Value: replacement]
            $browser_array = $browsersList->browserList;

            foreach ($browser_array as $index => $value) {
                if (str_contains($raw, $index)) {
                    $out = $value;
                }
            }

            return $out;
        }

        //Get visitor OS
        public function getVisitorOS() { 

            //Get user agent
            $agent = $_SERVER["HTTP_USER_AGENT"];
        
            //Define default OS
            $os_platform  = "Unknown OS";
        
            //OS array
            $os_array = array (
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
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile'
            );
        
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $agent)) {
                    $os_platform = $value;
                }
            }
        
            return $os_platform;
        }

        //First visit site
        public function firstVisit() {
            
            global $mysqlUtils;
            global $mainUtils;

            //Get data
            $visited_sites = 1;
            $first_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);
            $location = $mysqlUtils->escapeString($this->getVisitorLocation($ip_adress), true, true);
            $os = $mysqlUtils->escapeString($this->getVisitorOS(), true, true);
            
            //Check if ip is banned in database
            if ($this->isVisitorBanned($ip_adress)) {
                $banned = "yes";
            } else {
                $banned = "no";
            }

            //Save firt visit
            $mysqlUtils->insertQuery("INSERT INTO `visitors`(`visited_sites`, `first_visit`, `last_visit`, `browser`, `os`, `location`, `banned`, `ip_adress`) VALUES ('$visited_sites', '$first_visit', '$last_visit', '$browser', '$os', '$location', '$banned', '$ip_adress')");   

            //Rdirect banned users to banned page
            if ($this->isVisitorBanned($ip_adress)) {

                //Log trying to access site if user banned
                $mysqlUtils->logToMysql("Banned", "Banned user with ip: ".$ip_adress." trying to access site");

                //Redirect to banned page
                die("'<script type='text/javascript'>window.location.replace('/ErrorHandlerer.php?code=banned');</script>'"); 
            }
        }

        //Visit site
        public function visitSite() {

            global $mysqlUtils;
            global $dashboardController;
            global $mainUtils;
            global $pageConfig;
            global $adminController;

            //Get visitor ip
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);

            //Check if visitors count is zero
            if ($dashboardController->getVisitorsCount() == "0") {
                $this->firstVisit();
            } else {

                //Check if visitor exist in table
                if ($this->ifVisitorIsInTable($ip_adress)) {

                    //Get key count in db for duplicity check
                    $ip_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `ip_adress`='$ip_adress'"))["count"];

                    //Get id = 1 count
                    $id_one_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `id`='1'"))["count"];


                    //Check if key is not exist in database
                    if ($ip_count == "0") {
                        $this->firstVisit();

                    } else {
                        //Get data from mysql by IP
                        $visited_sites = intval($mysqlUtils->readFromMysql("SELECT visited_sites FROM visitors WHERE `ip_adress` = '".$ip_adress."'", "visited_sites"));

                        //New values to insert
                        $visited_sites = $visited_sites + 1;
                        $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
                        $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
                        $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);
                        $os = $mysqlUtils->escapeString($this->getVisitorOS(), true, true);

                        //Update database
                        $mysqlUtils->insertQuery("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET last_visit = '$last_visit' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET browser = '$browser' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET os = '$os' WHERE `ip_adress` = '$ip_adress'");  

                        //Show ban page if IP banned
                        if($this->isVisitorBanned($ip_adress)) {

                            //Log trying to access site if user banned
                            $mysqlUtils->logToMysql("Banned", "Banned user with ip: ".$ip_adress." trying to access site");

                            //Redirect to banned page
                            die("'<script type='text/javascript'>window.location.replace('/ErrorHandlerer.php?code=banned');</script>'"); 
                        }
                    }
                    
                } else {
                    $this->firstVisit();
                }

            }
        }

        //Check if visitor is banned
        public function isVisitorBanned($ip) {

            global $mysqlUtils;
            global $pageConfig;

            //Get ip count
            $ip_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `ip_adress`='$ip'"))["count"];
            $ip_count = intval($ip_count);
            
            //Check if ip is in database
            if ($ip_count > 0) {

                //Get banned status
                $banned_status = $mysqlUtils->readFromMysql("SELECT banned FROM visitors WHERE `ip_adress` = '".$ip."'", "banned");

                //Check if banned status = yes
                if ($banned_status == "yes") {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        //Get visitor location
        public function getVisitorLocation($ip) {

            global $pageConfig;

            //Check if site running on localhost
            if (($pageConfig->getValueByName("url") == "localhost") or ($pageConfig->getValueByName("url") == "127.0.0.1") or (str_starts_with($pageConfig->getValueByName("url"), "192.168"))) {
                $country = "HOST";
                $city = "Location";
            
            } else {
 
                //Get data by IP from ipinfo API 
                $details = json_decode(file_get_contents("http://ipinfo.io/$ip/json?token=".$pageConfig->getValueByName(("IPinfoToken"))));
           
                //Get country and site from API data
                $country = $details->country;
                $city = $details->city;
            }

            //Set undefined if country is empty
            if (empty($country)) {
                $country = "Undefined";
            }

            //Set undefined city is empty
            if (empty($city)) {
                $city = "Undefined";
            }

            //Final return
            return $country."/".$city;
        }

        //Get user ip by id
        public function getVisitorIPByID($id) {

            global $mysqlUtils;
            global $pageConfig;

            //Get ID count
            $ID_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `id`='$id'"))["count"];

            //Check if key found in database
            if ($ID_count == "0") {
                return NULL;
            } else {

                //Get visitor ip by key
                $visitorIP = $mysqlUtils->readFromMysql("SELECT ip_adress FROM visitors WHERE `id` = '".$id."'", "ip_adress");

                return $visitorIP;
            }
        }

        //Get user ip by ip
        public function getVisitorIDByIP($ip) {

            global $mysqlUtils;
            global $pageConfig;

            //Get IP count
            $IP_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `ip_adress`='$ip'"))["count"];

            //Check if key found in database
            if ($IP_count == "0") {
                return NULL;
            } else {

                //Get visitor id by ip
                $visitorID = $mysqlUtils->readFromMysql("SELECT id FROM visitors WHERE `ip_adress` = '".$ip."'", "id");

                return $visitorID;
            }
        }

        //Ban user by IP
        public function bannVisitorByIP($ip) {
            global $mysqlUtils;

            $mysqlUtils->insertQuery("UPDATE visitors SET banned = 'yes' WHERE `ip_adress` = '$ip'");
        }
 
        //UnBan user by IP
        public function unbannVisitorByIP($ip) {
            global $mysqlUtils;

            $mysqlUtils->insertQuery("UPDATE visitors SET banned = 'no' WHERE `ip_adress` = '$ip'");
        }

        //Check if visitor is in table
        public function ifVisitorIsInTable($ip) {

            global $mysqlUtils;
            global $pageConfig;

            //Get IP count from visitors table
            $ip_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `ip_adress`='$ip'"))["count"];
            $ip_count = intval($ip_count);    
            
            if ($ip_count > 0) {
                return true;
            } else {
                return false;
            }

        }

        //Call visit or first visit function
        public function init() {

            global $mysqlUtils;
            global $mainUtils;
            global $pageConfig;

            //Get value banned russia
            $bannedRussia = $pageConfig->getValueByName('bannedRussia');

            //Get user ip
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);


            //Check russia banned
            if ($bannedRussia == true) {
                
                //Check if user is russian
                if (str_starts_with(strtolower($this->getVisitorLocation($ip_adress)), "ru")) {

                    //Redirect to banned page
                    die("'<script type='text/javascript'>window.location.replace('/ErrorHandlerer.php?code=bannedRussia');</script>'"); 
                }
            }

            //Check if cookie seted
            if ($this->ifVisitorIsInTable($ip_adress)) {
                $this->visitSite();
            } else {
                $this->firstVisit();
            }
        }
    }
?>