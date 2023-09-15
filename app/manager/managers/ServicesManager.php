<?php // services manager (for admin dashboard panel)

    namespace becwork\managers;

    class ServicesManager {

        // method for getting true or false for process id returned running
        public function is_process_running($process): bool {
            
            // default state output
			$state = false;

            // execute cmd
            exec("pgrep ".$process, $pids);
            
            // check if outputed pid
            if(empty($pids)) {
                $state = false;
            } 
        
            return $state;
        }

        // method for getting true or false for service running
        public function is_service_active($service): bool {
            
            // default state output
			$state = false;

            // execute cmd
            $output = shell_exec("systemctl is-active $service");
            
            // check if service running
            if (trim($output) == "active") {
                $state = true;
            }

            return $state;
        }

        // method for getting true or false for ufw running
        public function is_ufw_running(): bool {

            // default state output
			$state = false;

            // execute cmd
            $output = shell_exec("sudo ufw status");

            // check if ufw running
            if (str_starts_with($output, "Status: active")) {
                $state = true;
            }

            return $state;
        }

        // method for getting true or false for service running
        public function is_service_installed($service_name): bool {
            
            global $config;

            // default state output
			$state = false;

            // get service dir from config
            $service_dir = $config->get_value('service-dir');

            // minecraft server
            if ($service_name == "minecraft") {

                // check if minecraft installed
                if (file_exists($service_dir."/minecraft/")) {
                    $state = true;
                }

            // teamspeak server
            } elseif ($service_name == "ts3server") {

                // check if teamspeak installed
                if (file_exists($service_dir."/teamspeak/")) {
                    $state = true;
                } 

            // check others (for systemctl)
            } else {

                // execute cmd
                $output = shell_exec("which $service_name");
                
                // check if output is empty
                if (!empty($output)) {
                    $state = true;
                }
            }

            return $state;
        }
        
        // check if screen session running
        public function is_screen_session_running($session_name): bool {

            // default state output
			$state = false;

            // execite cmd
            $exec = shell_exec("sudo screen -S $session_name -Q select . ; echo $?");

            // check if exec get output
            if ($exec == "0") {
                $state = true;
            }

            return $state;
        }

        // execute system command
        public function execute_command($command): void {

            // execite command
            shell_exec($command);
        }

        // execute bash/sh script form /scripts in web [Input: script name]
        public function executeScriptAsROOT($script_name): void {

            // execute script
            shell_exec("sudo runuser -l root -c 'sh ".$_SERVER['DOCUMENT_ROOT']."/../scripts/".$script_name."'");
        }
    }
?>