<?php //Visitor system controller & manager

    class VisitorSystemController {

        //Get user browser
        public function getBrowser() {

            $agent = $_SERVER["HTTP_USER_AGENT"];

            if( preg_match('/MSIE (\d+\.\d+);/', $agent) ) {
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
            } else if ($agent == null) {
                $browser = "Undefined";
            } else {
                $browser = $agent;
            }

            return $browser;
        }


        
        //First visit site
        public function firstVisit() {
            
            global $stringUtils;
            global $mysqlUtils;
            global $pageConfig;
            global $cookieUtils;

            if (isset($_COOKIE["identifier"])) {
                $cookieUtils->unset_cookie("identifier");
            }

            //Get data
            $key = $mysqlUtils->escapeString($stringUtils->genRandomStringAll(35), true, true);
            $visited_sites = 1;
            $first_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
            $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
            $ip_adress = $mysqlUtils->escapeString($_SERVER['REMOTE_ADDR'], true, true);

            //Get key count in db for duplicity check
            $key_count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors WHERE `key`='$key'"))["count"];

            //Check if key not duplicit and save
            if ($key_count == "0" && !isset($_COOKIE["identifier"])) {

                //Save firt visi
                $mysqlUtils->insertQuery("INSERT INTO `visitors`(`key`, `visited_sites`, `first_visit`, `last_visit`, `browser`, `ip_adress`) VALUES ( '$key', '$visited_sites', '$first_visit', '$last_visit', '$browser', '$ip_adress')");    
            
                //Set cookie
                $cookieUtils->cookieSet("identifier", $key, 2147483647);
            }

        }



        //Visit site
        public function visitSite() {

            global $mysqlUtils;
            global $dashboardController;

            if ($dashboardController->getVisitorsCount() == "0") {
                $this->firstVisit();
            } else {

                //Check if cookie seted
                if (isset($_COOKIE["identifier"])) {

                    //Get key from cookie
                    $key = $_COOKIE["identifier"];

                    //Get data from mysql by cookie identifier
                    $visited_sites = intval($mysqlUtils->readFromMysql("SELECT visited_sites FROM visitors WHERE `key` = '".$key."'", "visited_sites"));

                    //New values to insert
                    $visited_sites = $visited_sites + 1;
                    $last_visit = $mysqlUtils->escapeString(date('d.m.Y H:i'), true, true);
                    $browser = $mysqlUtils->escapeString($this->getBrowser(), true, true);
                    $ip_adress = $mysqlUtils->escapeString($_SERVER['REMOTE_ADDR'], true, true);


                    //Update database
                    $mysqlUtils->insertQuery("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `key` = '$key'");
                    $mysqlUtils->insertQuery("UPDATE visitors SET last_visit = '$last_visit' WHERE `key` = '$key'");
                    $mysqlUtils->insertQuery("UPDATE visitors SET browser = '$browser' WHERE `key` = '$key'");
                    $mysqlUtils->insertQuery("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `key` = '$key'");
                } else {
                    $this->firstVisit();
                }

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