<?php // dashboard manager (system, database information getters)
  
    namespace becwork\managers;

    class DashboardManager {

        // get server uptime
        public function getUpTime() {

            // get data
            $ut = strtok(exec("cat /proc/uptime"), ".");
            $days = sprintf("%2d", ($ut/(3600*24)));
            $hours = sprintf("%2d", (($ut % (3600*24))/3600));
            $min = sprintf("%2d", ($ut % (3600*24) % 3600)/60);
            $sec = sprintf("%2d", ($ut % (3600*24) % 3600)%60);
        
            // get data array
            $arr = array($days, $hours, $min, $sec);

            // format uptimee
            $uptime = "Days: $arr[0], Hours: $arr[1], Min: $arr[2]";

            return $uptime;
        }
        
        // get CPU usage %
        public function getCPUProc() {
            
            // default load value
            $load = 100;
            
            $loads = sys_getloadavg();
            $core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
            $load = round($loads[0]/($core_nums + 1)*100, 2);
            
            if ($load > 100) {
                $load = 100;
            } else {
                $load = $load;
            }

            return $load;
        }

        // get memory (RAM) usage
        public function getMemoryInfo() {
            exec('cat /proc/meminfo', $memory_raw);
            $memory_free = 0;
            $memory_total = 0;
            $memory_used = 0;
            for($i = 0; $i < count($memory_raw); $i++){
                if(strstr($memory_raw[$i], 'MemTotal')){
                    $memory_total = filter_var($memory_raw[$i], FILTER_SANITIZE_NUMBER_INT);
                    $memory_total = $memory_total / 1048576;
                }
                if(strstr($memory_raw[$i], 'MemFree')){
                    $memory_free = filter_var($memory_raw[$i], FILTER_SANITIZE_NUMBER_INT);
                    $memory_free = $memory_free / 1048576;
                }
            }
            $memory_used = $memory_total - $memory_free;
            return array(
                'used'	=>	number_format($memory_used, 2),
                'free'	=>	number_format($memory_free, 2),
                'total'	=>	number_format($memory_total, 2)
            );	
        }
        
        // get hard drive space usage in %
        public function getDrivesInfo() {
            $output = exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
            return $output;
        }

        // get software / kernal information
        public function getSoftwareInfo() {
            $softwares = array();
            $software = array();
            $iteration = 0;
            $software_key = '';
            $distro = array();
            exec('rpm -qai | grep "Name        :\|Version     :\|Release     :\|Install Date:\|Group       :\|Size        :"', $software_raw);
            for($i = 0; $i < count($software_raw); $i++){
                preg_match_all('/(?P<name1>.+): (?P<val1>.+) (?P<name2>.+): (?P<val2>.+)/', $software_raw[$i], $matches);
                if(empty($matches['name1'])) continue;
                if(trim($matches['name1'][0]) == 'Name') $software_key = strtolower(trim(str_replace(array('-', 'Build', 'Source'), array('_', '', ''), $matches['val1'][0])));
                $softwares[$software_key][strtolower(str_replace(' ', '_', trim($matches['name1'][0])))] = trim(str_replace(array('Build', 'Source'), '', $matches['val1'][0]));
                $softwares[$software_key][strtolower(str_replace(' ', '_', trim($matches['name2'][0])))] = trim(str_replace(array('Build', 'Source'), '', $matches['val2'][0]));
            }
            ksort($softwares);
            foreach($softwares as $s){
                $software[] = $s;	
            }
            exec('uname -mrs', $distro_raw);
            exec('cat /etc/*-release', $distro_name_raw);
            $distro_parts = explode(' ', $distro_raw[0]);
            $distro['operating_system'] = $distro_name_raw[0];
            $distro['kernal_version'] = $distro_parts[0] . ' ' . $distro_parts[1];
            $distro['kernal_arch'] = $distro_parts[2];
            return array(
                'packages'	=> $software,
                'distro'	=> $distro
            );
        }

        // get pastes count
        public function getPastesCount() {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM pastes");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get log count
        public function getLogsCount() {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get login logs count
        public function getLoginLogsCount() {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs WHERE name LIKE '%Login%' or name LIKE '%Logout%'");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get unreaded logs count
        public function getUnreadedLogs() {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs WHERE status LIKE '%unreaded%'");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get page visitors count
        public function getVisitorsCount() {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM visitors");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get MSGS in inbox count
        public function getMSGSCount() {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM messages WHERE status='open'");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get todos count in todos table
        public function getTodosCount() {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM todos WHERE status='open'");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }
        
        // get images count in gallery
        public function getImagesCount() {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM image_uploader");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // get banned visitors count 
        public function getBannedCount() { 

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM banned WHERE status='banned'");

            // count data
            $dataCount = count($data);

            return $dataCount;
        }

        // check if system is linux
        public function isSystemLinux() {

            // default state value
            $state = false;

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
                $state = true;
            } 
            
            return $state;
        }

        // check if warnings box empty
        public function isWarninBoxEmpty() {

            global $config, $mainUtils, $siteManager, $servicesManager;

            // default state value
            $state = true;

            // check if service directory exist in system
            if (!file_exists($config->getValue('serviceDir'))) {
                $state = false;

            // check if site running on ssl connction
            } elseif (!$mainUtils->isSSL()) {
                $state = false;

            // check if hard drive is not full
            } elseif ($this->getDrivesInfo() > 89) {
                $state = false;
            
            // check if antilog cookie not empty
            } elseif (empty($_COOKIE[$config->getValue("antiLogCookie")])) {
                $state = false;

            // check if found new logs
            } elseif (($this->getUnreadedLogs()) != "0" && (!empty($_COOKIE[$config->getValue("antiLogCookie")]))) {
                $state = false;

            // check if found new msgs in inbox
            } elseif ($this->getMSGSCount() != "0") {
                $state = false;

            // check if maintenance is enabled
            } elseif ($config->getValue("maintenance") == "enabled") {
                $state = false;

            // check if dev-mode is enabled
            } elseif ($siteManager->isSiteDevMode()) {
                $state = false;
            }

            return $state;
        }
    }
?>
 