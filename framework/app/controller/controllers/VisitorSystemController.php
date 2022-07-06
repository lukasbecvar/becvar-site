<?php //Visitor system controller & manager

    class VisitorSystemController {

        //Get user browser
        public function getBrowser() {

            //Get user agent
            $agent = $_SERVER["HTTP_USER_AGENT"];

            //Build browser agent from http agent
            if(preg_match('/MSIE (\d+\.\d+);/', $agent) ) {
                $browser = "Internet Explore";
           
            } else if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $agent) ) {
                $browser = "Chrome";
            
            } else if (preg_match('/Edge\/\d+/', $agent) ) {
                $browser = "Edge";
            
            } else if ( preg_match('/Firefox[\/\s](\d+\.\d+)/', $agent) ) {
                $browser = "Firefox";
            
            } else if ( preg_match('/OPR[\/\s](\d+\.\d+)/', $agent) ) {
                $browser = "Opera";
            
            } else if (preg_match('/Safari[\/\s](\d+\.\d+)/', $agent) ) {
                $browser = "Safari";
 
            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $agent) ) {
                $browser = "Opera";

            } else if (str_contains($agent, "Dalvik")) {
                $browser = "Dalvik/Android";
           
            } else if (str_contains($agent, "Dalvik")) {
                $browser = "Dalvik/Android";

            } else if (str_contains($agent, "Googlebot")) {
                $browser = "Googlebot";

            } else if (str_contains($agent, "Discordbot")) {
                $browser = "Discordbot";

            } else if (str_contains($agent, "YandexBot")) {
                $browser = "YandexBot";

            } else if (str_contains($agent, "tchelebi")) {
                $browser = "tchelebi";

            } else if (str_contains($agent, "NetSystemsResearch")) {
                $browser = "NetSystemsResearch";

            } else if (str_contains($agent, "NetcraftSurveyAgent")) {
                $browser = "NetcraftSurveyAgent";

            } else if (str_contains($agent, "CensysInspect")) {
                $browser = "CensysInspect";

            } else if (str_contains($agent, "PolycomRealPresenceTrio")) {
                $browser = "PolycomRealPresenceTrio";

            } else if (str_contains($agent, "masscan-ng")) {
                $browser = "masscan-ng scanner";

            } else if (str_contains($agent, "Baiduspider")) {
                $browser = "Baiduspider";

            } else if (str_contains($agent, "SemrushBot")) {
                $browser = "SemrushBot";

            } else if (str_contains($agent, "Firefox/96")) {
                $browser = "Firefox/96";

            } else if($agent == "python-requests/2.6.0 CPython/2.7.5 Linux/3.10.0-1160.el7.x86_64") {
                $browser = "python-requests/2.6.0";

            //Return undefined
            } else if ($agent == null) {
                $browser = "Undefined";
           
            //Return aget
            } else {
                $browser = $agent;
            }
            
            //Return browser ID
            return $browser;
        }


        
        //First visit site
        public function firstVisit() {
            
            global $stringUtils;
            global $mysqlUtils;
            global $pageConfig;
            global $cookieUtils;
            global $mainUtils;

            if (isset($_COOKIE["identifier"])) {
                $cookieUtils->unset_cookie("identifier");
            }

            //Get data
            $key = $mysqlUtils->escapeString($stringUtils->genRandomStringAll(35), true, true);
            $visited_sites = 1;
            $first_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);
            $location = $mysqlUtils->escapeString($this->getVisitorLocation($mainUtils->getRemoteAdress()), true, true);

            //Get key count in db for duplicity check
            $key_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `key`='$key'"))["count"];

            //Check if key not duplicit and save
            if ($key_count == "0" && !isset($_COOKIE["identifier"])) {

                //Save firt visi
                $mysqlUtils->insertQuery("INSERT INTO `visitors`(`key`, `visited_sites`, `first_visit`, `last_visit`, `browser`, `location`, `ip_adress`) VALUES ( '$key', '$visited_sites', '$first_visit', '$last_visit', '$browser', '$location', '$ip_adress')");    
            
                //Set cookie
                $cookieUtils->cookieSet("identifier", $key, 2147483647);
            }

        }



        //Visit site
        public function visitSite() {

            global $mysqlUtils;
            global $dashboardController;
            global $mainUtils;
            global $pageConfig;

            if ($dashboardController->getVisitorsCount() == "0") {
                $this->firstVisit();
            } else {

                //Check if cookie seted
                if (isset($_COOKIE["identifier"])) {

                    //Get key form identifier cookie
                    if (!empty($_COOKIE["identifier"])) {
                        $key = $mysqlUtils->escapeString($_COOKIE["identifier"], true, true);
                    } else {
                        die('<script type="text/javascript">window.location.replace("ErrorHandlerer.php?code=400");</script>');
                    }

                    //Get key count in db for duplicity check
                    $key_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `key`='$key'"))["count"];

                    //Check if key is not exist in database
                    if ($key_count == "0") {
                        $this->firstVisit();

                    } else {
                        //Get key from cookie
                        $key = $_COOKIE["identifier"];

                        //Get data from mysql by cookie identifier
                        $visited_sites = intval($mysqlUtils->readFromMysql("SELECT visited_sites FROM visitors WHERE `key` = '".$key."'", "visited_sites"));

                        //New values to insert
                        $visited_sites = $visited_sites + 1;
                        $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
                        $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
                        $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);
                        $location = $mysqlUtils->escapeString($this->getVisitorLocation($mainUtils->getRemoteAdress()), true, true);

                        //Update database
                        $mysqlUtils->insertQuery("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `key` = '$key'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET last_visit = '$last_visit' WHERE `key` = '$key'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET browser = '$browser' WHERE `key` = '$key'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `key` = '$key'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET location = '$location' WHERE `key` = '$key'");
                    }
                    
                } else {
                    $this->firstVisit();
                }

            }
        }



        //Get visitor details
        public function getVisitorDetails($ip) {

            //Check if ip not localhost
            if ($ip != "localhost" or $ip != "127.0.0.1" or $ip != "192.168.0.103") {
                $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
            } else {
                $details = NULL;
            }
            
            return $details;
        }



        //Get visitor location
        public function getVisitorLocation($ip) {

            //Check if ip is localhost
            if ($ip == "localhost" or $ip == "127.0.0.1" or $ip == "192.168.0.103") {
                return "HOST/Location";
            } else {
                
                $country = $this->getVisitorDetails($ip)->country;
                $city = $this->getVisitorDetails($ip)->city;
                
                if (empty($country)) {
                    $country = "Undefined";
                }

                if (empty($city)) {
                    $city = "Undefined";
                }

                return $country."/".$city;
            }
        }



        //Call visit or first visit function
        public function init() {

            //Check if cookie seted
            if (isset($_COOKIE["identifier"])) {
                $this->visitSite();
            } else {
                $this->firstVisit();
            }
        }
    }
?>
