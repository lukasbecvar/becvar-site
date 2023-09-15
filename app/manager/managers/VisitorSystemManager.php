<?php // visitor system manager

    namespace becwork\managers;

    class VisitorSystemManager {

        // get user browser
        public function get_browser(): ?string {

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
        public function get_short_browser_id($raw): ?string {
            
            global $browsers_list;

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

            // identify Opera
            } else if (preg_match('/OPR[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Opera";
                $found = "yes";

            } else if (preg_match('/Opera[\/\s](\d+\.\d+)/', $raw)) {
                $out = "Opera";
                $found = "yes";
            }

            // identify shortify array [ID: str_contains, Value: replacement]
            $browser_array = $browsers_list->browser_list;

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

            // return output
            return $out;
        }

        // get visitor OS
        public function get_visitor_os(): ?string { 

            // get user agent
            $agent = $this->get_browser();
        
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
        public function first_visit(): void {
            
            global $mysql, $main_utils, $site_manager, $escape_utils;

            // default visited value
            $visited_sites = 1;

            // get date
            $date = date('d.m.Y H:i');;

            // get date values
            $first_visit = $date;
            $last_visit = $date;

            // get & escape visitor browser ID
            $browser = $escape_utils->special_chars_strip($this->get_browser());

            // get & escape visitor IP
            $ip_adress = $escape_utils->special_chars_strip($main_utils->get_remote_adress());

            // get & escape visitor location
            $location = $escape_utils->special_chars_strip($this->get_visitor_location($ip_adress));

            // get & escape visitor OS
            $os = $escape_utils->special_chars_strip($this->get_visitor_os());

            // check if ip is banned in database
            if ($this->is_visitor_banned($ip_adress)) {
                $banned = "yes";
            } else {
                $banned = "no";
            }

            // insert firt visit
            $mysql->insert("INSERT INTO `visitors`(`visited_sites`, `first_visit`, `last_visit`, `browser`, `os`, `location`, `ip_adress`) VALUES ('$visited_sites', '$first_visit', '$last_visit', '$browser', '$os', '$location', '$ip_adress')");   

            // redirect banned users to banned page
            if ($this->is_visitor_banned($ip_adress)) {

                $mysql->log("banned", "banned user with ip: ".$ip_adress." trying to access site");

                // redirect to banned page
                $site_manager->redirect_error("banned");
            }
        }

        // visit site
        public function visit_site(): void {

            global $mysql, $dashboard_manager, $main_utils, $site_manager, $escape_utils;;

            // get & escape visitor ip
            $ip_adress = $escape_utils->special_chars_strip($main_utils->get_remote_adress());

            // check if visitors count is zero
            if ($dashboard_manager->get_visitors_count() == "0") {
                $this->first_visit();
            } else {

                // check if visitor exist in table
                if ($this->is_visitorIs_in_table($ip_adress)) {

                    // get key count in db for duplicity check
                    $ip_ids = $mysql->fetch("SELECT id FROM visitors WHERE `ip_adress`='$ip_adress'");

                    // check if key is not exist in database
                    if (count($ip_ids) == 0) {
                        $this->first_visit();

                    } else {
                        // get data from mysql by IP
                        $visited_sites = intval($mysql->fetch_value("SELECT visited_sites FROM visitors WHERE `ip_adress` = '".$ip_adress."'", "visited_sites"));

                        // new values to update
                        $visited_sites = $visited_sites + 1;
                        $last_visit = $escape_utils->special_chars_strip(date('d.m.Y H:i'));
                        $browser = $escape_utils->special_chars_strip($this->get_browser());
                        $ip_adress = $escape_utils->special_chars_strip($main_utils->get_remote_adress());
                        $os = $escape_utils->special_chars_strip($this->get_visitor_os());

                        // update database
                        $mysql->insert("UPDATE visitors SET visited_sites = '$visited_sites' WHERE `ip_adress` = '$ip_adress'");
                        $mysql->insert("UPDATE visitors SET last_visit = '$last_visit' WHERE `ip_adress` = '$ip_adress'");
                        $mysql->insert("UPDATE visitors SET browser = '$browser' WHERE `ip_adress` = '$ip_adress'");
                        $mysql->insert("UPDATE visitors SET ip_adress = '$ip_adress' WHERE `ip_adress` = '$ip_adress'");
                        $mysql->insert("UPDATE visitors SET os = '$os' WHERE `ip_adress` = '$ip_adress'");  

                        // check if ip in database is Unknown
                        if ($this->get_visitor_location_from_database($this->get_visitor_id_by_ip($ip_adress)) == "Unknown") {

                            // get location 
                            $location = $escape_utils->special_chars_strip($this->get_visitor_location($ip_adress));

                            // insert location
                            $mysql->insert("UPDATE visitors SET location = '$location' WHERE `ip_adress` = '$ip_adress'");  
                        }

                        // show ban page if IP banned
                        if($this->is_visitor_banned($ip_adress)) {

                            $mysql->log("banned", "banned user with ip: ".$ip_adress." trying to access site");

                            // redirect to banned page
                            $site_manager->redirect_error("banned");
                        }
                    }
                    
                } else { // init first visit for new visitors
                    $this->first_visit();
                }
            }
        }

        // check if visitor is banned
        public function is_visitor_banned($ip): bool {

            global $mysql;

            // default banned state value
            $state = false;

            // get ip ids
            $ip_ids = $mysql->fetch("SELECT id FROM banned WHERE `ip_adress`='$ip'");
            
            // check if ip is in database
            if (count($ip_ids) > 0) {

                // get banned status
                $banned_status = $mysql->fetch_value("SELECT status FROM banned WHERE `ip_adress` = '".$ip."'", "status");

                // check if banned status = yes
                if ($banned_status == "banned") {
                    $state = true;
                }
            }

            return $state;
        }

        // get visitor location from table
        public function get_visitor_location_from_database($id): ?string {

            global $mysql;

            // default location value
            $location = null;

            // get visitor data by id
            $visitor = $mysql->fetch("SELECT * FROM visitors WHERE `id` = '$id'");

            // check if visitor found in database
            if (count($visitor) > 0) {

                // get visitor location from database
                $location = $mysql->fetch_value("SELECT location FROM visitors WHERE `id` = '$id'", "location");

            } else {
                $location = "Unknown";
            }

            return $location;
        }

        // get visitor location
        public function get_visitor_location($ip): ?string {

            global $mysql, $config, $site_manager;

            // default location value
            $location = null;

            // check if site running on localhost
            if ($site_manager->is_running_localhost()) {
                $country = "HOST";
                $city = "Location";
            
            } else {
 
                // try get data
                try {

                    // get geoplugin url
                    $geoplugin_url = $config->get_value("geoplugin-url");

                    // get geoplugin data
                    $geoplugin_data = file_get_contents($geoplugin_url."/json.gp?ip=$ip");

                    // decode data
                    $details = json_decode($geoplugin_data);
        
                    // get country and site from API data
                    $country = $details->geoplugin_countryCode;

                    // check if city name defined
                    if (!empty(explode("/", $details->geoplugin_timezone)[1])) {
                        
                        // get city name from timezone (explode /)
                        $city = explode("/", $details->geoplugin_timezone)[1];
                    } else {
                        $city = null;
                    }

                } catch (\Exception $e) {

                    // set null if data not getted
                    $country = null;
                    $city = null;

                    $mysql->log("geolocate-error", "error to geolocate ip: " . $ip . ", error: " . $e->getMessage());
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
                $location = "Unknown";
            } else {
                $location = $country."/".$city;
            }

            return $location;
        }

        // get user ip by id
        public function get_visitor_ip_by_id($id): ?string {

            global $mysql;

            // get IDs
            $ids = $mysql->fetch("SELECT id FROM visitors WHERE `id`='$id'");

            // check if key found in database
            if (count($ids) == 0) {
                return null;
            } else {

                // get visitor ip by key
                $visitor_ip = $mysql->fetch_value("SELECT ip_adress FROM visitors WHERE `id` = '".$id."'", "ip_adress");

                // return ip
                return $visitor_ip;
            }
        }

        // get user ip by ip
        public function get_visitor_id_by_ip($ip): ?int {

            global $mysql;

            // get IDs by IP
            $ids = $mysql->fetch("SELECT id FROM visitors WHERE `ip_adress` = '$ip'");

            // check if key found in database
            if (count($ids) == 0) {
                return null;
            } else {

                // get visitor id by ip
                $visitor_id = $mysql->fetch_value("SELECT id FROM visitors WHERE `ip_adress` = '".$ip."'", "id");

                return $visitor_id;
            }
        }

        // ban user by IP
        public function ban_visitor_by_ip($ip, $reason): void {
            
            global $mysql;

            // get IP count from banned table
            $ids = $mysql->fetch("SELECT id FROM banned WHERE `ip_adress`='$ip'");

            // check if ip found in banned table
            if (count($ids) > 0) {

                // update ban status
                $mysql->insert("UPDATE banned SET status = 'banned' WHERE `ip_adress` = '$ip'");

                // update reason
                $mysql->insert("UPDATE banned SET reason = '$reason' WHERE `ip_adress` = '$ip'");

            } else {
                // default banned status
                $status = "banned";

                // get current date
                $banned_date = date("d.m.Y");

                // insert ban users
                $mysql->insert("INSERT INTO `banned`(`ip_adress`, `reason`, `banned_date`, `status`) VALUES ('$ip', '$reason', '$banned_date', '$status')");
            }
        }
 
        // un-ban user by IP
        public function unban_visitor_by_ip($ip): void {
            
            global $mysql;

            // update ban status
            $mysql->insert("UPDATE banned SET status = 'unbanned' WHERE `ip_adress` = '$ip'");
        }

        // check if visitor is in table
        public function is_visitorIs_in_table($ip): bool {

            global $mysql;

            // default state
            $state = false;

            // get IDs where ip
            $ids = $mysql->fetch("SELECT id FROM visitors WHERE `ip_adress` = '$ip'");
            
            // check if ip found
            if (count($ids) > 0) {
                $state = true;
            }

            return $state;
        }

        // call visit or first visit function
        public function init(): void {

            global $mysql, $config, $main_utils, $site_manager, $escape_utils;

            // get & escape user ip
            $ip_adress = $escape_utils->special_chars_strip($main_utils->get_remote_adress());

            // check if visitor found in database by IP
            if ($this->is_visitorIs_in_table($ip_adress)) {
                $this->visit_site();
            
            // insert new visitor to database
            } else {
                $this->first_visit();
            }
        }
    }
?>