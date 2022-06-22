<?php
 
    class DashboardController {

        /**
         * Method for getting server uptime
         *
         * @return Array
        **/
        public function getUpTime() {
            $ut = strtok(exec("cat /proc/uptime"), ".");
            $days = sprintf("%2d", ($ut/(3600*24)));
            $hours = sprintf("%2d", (($ut % (3600*24))/3600));
            $min = sprintf("%2d", ($ut % (3600*24) % 3600)/60);
            $sec = sprintf("%2d", ($ut % (3600*24) % 3600)%60);
        
            $arr = array( $days, $hours, $min, $sec );

            return "Days: $arr[0], Hours: $arr[1], Min: $arr[2]";
        
        }
        


        /**
         * Method for getting cpu information
         *
         * @return Array
        **/
        public function getCPUProc() {
            $loads = sys_getloadavg();
            $core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
            $load = round($loads[0]/($core_nums + 1)*100, 2);
            return $load;
        }
        


        /**
         * Method for getting memory information
         *
         * @return Array
        **/
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
        


        /**
         * Method for getting hard drive information
         *
        **/
        public function getDrivesInfo() {
            $output = exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
            return $output;
        }
        


        /**
         * Method for getting software / kernal information
         *
         * @return Array
        **/
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




        //Get pastes count
        public function getPastesCount() {
            global $mysqlUtils;
            global $pageConfig;

            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM pastes"))["count"];
        }
        
        

        //Get log count
        public function getLogsCount() {
            global $mysqlUtils;
            global $pageConfig;

            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM logs"))["count"];
        }




        //Get login logs count
        public function getLoginLogsCount() {
            global $mysqlUtils;
            global $pageConfig;

            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM logs WHERE name LIKE '%Login%' or name LIKE '%Logout%'"))["count"];
        }

        


        //Get unreaded logs count
        public function getUnreadedLogs() {
            global $mysqlUtils;
            global $pageConfig;
        
            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM logs WHERE status LIKE '%unreader%'"))["count"];
           
        }



        //Get page visitors count
        public function getVisitorsCount() {
            global $mysqlUtils;
            global $pageConfig;
        
            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM visitors"))["count"];
           
        }




        //Get MSGS in inbox count
        public function getMSGSCount() {
            global $mysqlUtils;
            global $pageConfig;

            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM messages WHERE status='open'"))["count"];
        }



        //Get todos count in todos table
        public function getTodosCount() {
            global $mysqlUtils;
            global $pageConfig;

            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM todos WHERE status='open'"))["count"];
        }


        
        //Get images count in gallery
        public function getImagesCount() {
            global $mysqlUtils;
            global $pageConfig;
        
            return mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM image_uploader"))["count"];
        }
    }
?>
