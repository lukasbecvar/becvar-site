<?php // dashboard manager (system, database information getters)
  
    namespace becwork\managers;

    class DashboardManager {

        // get server uptime
        public function get_uptime(): string {

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
        public function get_cpu_usage(): float {
            
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
        public function get_ram_usage(): array {
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
        public function get_drive_usage(): string {
            $output = exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
            return $output;
        }

        // get software / kernal information
        public function get_software_info(): array {
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
        public function get_pastes_count(): ?int {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM pastes");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get log count
        public function get_logs_count(): ?int {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get login logs count
        public function get_login_logs_count(): ?int {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs WHERE name LIKE '%Login%' or name LIKE '%Logout%'");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get unreaded logs count
        public function get_unreaded_logs(): ?int {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM logs WHERE status LIKE '%unreaded%'");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get page visitors count
        public function get_visitors_count(): ?int {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM visitors");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get MSGS in inbox count
        public function get_messages_count(): ?int {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM messages WHERE status='open'");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get todos count in todos table
        public function get_todos_count(): ?int {

            global $mysql;

            // get data ids
            $data = $mysql->fetch("SELECT id FROM todos WHERE status='open'");

            // count data
            $data_count = count($data);
            return $data_count;
        }
        
        // get images count in gallery
        public function get_images_count(): ?int {

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM image_uploader");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // get banned visitors count 
        public function get_banned_count(): ?int { 

            global $mysql;
        
            // get data ids
            $data = $mysql->fetch("SELECT id FROM banned WHERE status='banned'");

            // count data
            $data_count = count($data);
            return $data_count;
        }

        // check if system is linux
        public function is_system_linux(): bool {

            // default state value
            $state = false;

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
                $state = true;
            } 
            
            return $state;
        }

        // check if warnings box empty
        public function is_warnin_box_empty(): bool {

            global $config, $main_utils, $site_manager, $services_manager;

            // default state value
            $state = true;

            // check if service directory exist in system
            if (!file_exists($config->get_value('service-dir'))) {
                $state = false;

            // check if site running on ssl connction
            } elseif (!$main_utils->is_ssl()) {
                $state = false;

            // check if hard drive is not full
            } elseif ($this->get_drive_usage() > 89) {
                $state = false;
            
            // check if antilog cookie not empty
            } elseif (empty($_COOKIE[$config->get_value("anti-log-cookie")])) {
                $state = false;

            // check if found new logs
            } elseif (($this->get_unreaded_logs()) != "0" && (!empty($_COOKIE[$config->get_value("anti-log-cookie")]))) {
                $state = false;

            // check if found new msgs in inbox
            } elseif ($this->get_messages_count() != "0") {
                $state = false;

            // check if maintenance is enabled
            } elseif ($config->get_value("maintenance") == "enabled") {
                $state = false;

            // check if dev-mode is enabled
            } elseif ($site_manager->is_dev_mode()) {
                $state = false;
            }

            return $state;
        }
    }
?>