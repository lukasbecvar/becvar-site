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
            
            global $mysqlUtils;
            global $mainUtils;

            //Get data
            $visited_sites = 1;
            $first_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);
            $location = $mysqlUtils->escapeString($this->getVisitorLocation($ip_adress), true, true);
            
            //Check if ip is banned in database
            if ($this->isVisitorBanned($ip_adress)) {
                $banned = "yes";
            } else {
                $banned = "no";
            }

            //Save firt visit
            $mysqlUtils->insertQuery("INSERT INTO `visitors`(`visited_sites`, `first_visit`, `last_visit`, `browser`, `location`, `banned`, `ip_adress`) VALUES ('$visited_sites', '$first_visit', '$last_visit', '$browser', '$location', '$banned', '$ip_adress')");   

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

                        //Update database
                        $mysqlUtils->insertQuery("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET last_visit = '$last_visit' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET browser = '$browser' WHERE `ip_adress` = '$ip_adress'");
                        $mysqlUtils->insertQuery("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `ip_adress` = '$ip_adress'");

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
            if (($pageConfig->getValueByName("url") == "localhost") or ($pageConfig->getValueByName("url") == "127.0.0.1") (str_starts_with($pageConfig->getValueByName("url"), "192.168.0"))) {
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

            //Get user ip
            $ip_adress = $mysqlUtils->escapeString($mainUtils->getRemoteAdress(), true, true);

            //Check if cookie seted
            if ($this->ifVisitorIsInTable($ip_adress)) {
                $this->visitSite();
            } else {
                $this->firstVisit();
            }
        }
    }
?>
