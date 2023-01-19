<?php // services controller

    namespace becwork\controllers;

    class ServicesController {

        /**
         * Method for getting true or false for process id returned running
         *
        **/
        public function ifProcessRunning($process) {
            
            // execute builder
            exec("pgrep ".$process, $pids);
            
            // check if outputed pid
            if(empty($pids)) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Method for getting true or false for service running
         *
        **/
        public function ifServiceActive($service) {
            
            // execute builder
            $output = shell_exec("systemctl is-active $service");
            
            // check if service running
            if (trim($output) == "active") {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Method for getting true or false for ufw running
         *
        **/
        public function isUFWRunning() {

            // execute builder
            $output = shell_exec("sudo ufw status");

            // check if ufw running
            if (str_starts_with($output, "Status: active")) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Method for getting true or false for service running
         *
        **/
        public function isServiceInstalled($serviceName) {
            
            global $pageConfig;

            // get service dir from config
            $serviceDir = $pageConfig->getValueByName('serviceDir');

            // minecraft server
            if ($serviceName == "minecraft") {

                // check if minecraft installed
                if (file_exists($serviceDir."/minecraft/")) {
                    return true;
                } else {
                    return false;
                }

            // teamspeak server
            } elseif ($serviceName == "ts3server") {

                // check if teamspeak installed
                if (file_exists($serviceDir."/teamspeak/")) {
                    return true;
                } else {
                    return false;
                }

            // check others (for systemctl)
            } else {

                // execute builder
                $output = shell_exec("which $serviceName");
                
                // check if output is empty
                if (empty($output)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        
        // check if screen session running
        public function checkScreenSession($sessionName) {

            // execite builder
            $exec = shell_exec("sudo screen -S $sessionName -Q select . ; echo $?");

            // check if exec get output
            if ($exec == "0") {
                return true;
            } else {
                return false;
            }
        }

        // execute system command
        public function executeCommand($command) {

            // execite command
            shell_exec($command);
        }

        // execute bash/sh script form /scripts in web [Input: script name]
        public function executeScriptAsROOT($scriptName) {

            global $pageConfig;

            // execute script
            shell_exec("sudo runuser -l root -c 'sh ".$_SERVER['DOCUMENT_ROOT']."/../scripts/".$scriptName."'");
        }
    }
?>