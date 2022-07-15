<?php /* The controller for services action */

    class ServicesController {


        /**
         * Method for getting true or false for process id returned running
         *
        **/
        public function ifProcessRunning($process) {
            
            exec("pgrep ".$process, $pids);
            
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
            
            $output = shell_exec("systemctl is-active $service");
            
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

            $output = shell_exec("sudo ufw status");

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

            //Get service dir from config
            $serviceDir = $pageConfig->getValueByName('serviceDir');

            //Minecraft server
            if ($serviceName == "minecraft") {
                if (file_exists($serviceDir."/minecraft/")) {
                    return true;
                } else {
                    return false;
                }

            //Teamspeak server
            } elseif ($serviceName == "ts3server") {
                if (file_exists($serviceDir."/teamspeak/")) {
                    return true;
                } else {
                    return false;
                }

            //Dubinek bot
            } elseif ($serviceName == "dubinek") {
                if (file_exists($serviceDir."/dubinek/")) {
                    return true;
                } else {
                    return false;
                }

            //Check others (for systemctl)
            } else {

                $output = shell_exec("which $serviceName");
                if (empty($output)) {
                    return false;
                } else {
                    return true;
                }
            }
        }


        
        //Check if screen session running
        public function checkScreenSession($sessionName) {
            $exec = shell_exec("sudo screen -S dubinek -Q select . ; echo $?");

            if ($exec == "0") {
                return true;
            } else {
                return false;
            }
        }



        //Execute system command
        public function executeCommand($command) {
            shell_exec($command);
        }


        //Execute bash/sh script form /scripts in web [Input: script name]
        public function executeScriptAsROOT($scriptName) {

            global $pageConfig;

            //Get service dir from config
            $serviceDir = $pageConfig->getValueByName('serviceDir');

            shell_exec("sudo runuser -l root -c 'sh ".$_SERVER['DOCUMENT_ROOT']."/../scripts/".$scriptName."'");
        }
    }
?>