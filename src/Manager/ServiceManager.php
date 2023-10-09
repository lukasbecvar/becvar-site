<?php

namespace App\Manager;

use App\Helper\LogHelper;
use App\Util\JsonUtil;

/*
    Service manager provides all services methods (start, stop, status)
*/

class ServiceManager
{
    private $jsonUtil;
    private $logHelper;

    public function __construct(JsonUtil $jsonUtil, LogHelper $logHelper)
    {
        $this->jsonUtil = $jsonUtil;
        $this->logHelper = $logHelper;
    }

    public function isServicesListExist(): bool 
    {
        $services_list = $this->jsonUtil->getJson(__DIR__.'/../../services.json');

        // check if list is null
        if ($services_list != null) {
            return true;
        } else {
            return false;
        }
    }

    public function getServices(): ?array 
    {
        $services_list = $this->jsonUtil->getJson(__DIR__.'/../../services.json');
        $services = [];  

        // check if services list load valid
        if ($services_list != null) {
           
            // execute separate service row
            foreach ($services_list as $index => $value) {
                
                // check if service is enabled
                if ($services_list[$index]['enable']) {

                    // UFW service service
                    if ($services_list[$index]['service_name'] == 'ufw') {

                        // build ufw service
                        $ufw = [
                            'service_name' => $services_list[$index]["service_name"],
                            'display_name' => $services_list[$index]["display_name"],
                            'start_cmd' => $services_list[$index]["start_cmd"],
                            'stop_cmd' => $services_list[$index]["stop_cmd"],
                            'enable' => $services_list[$index]["enable"]
                        ];

                        // get ufw status
                        if ($this->isUfwRunning()) {
                            $ufw += ['status' => 'online'];
                        } else {
                            $ufw += ['status' => 'offline'];
                        }

                        // add ufw array to services
                        array_push($services, $ufw);
                    } 
                    
                    // teamSpeak server service
                    elseif ($services_list[$index]['service_name'] == 'ts3server') {

                        // build teamSpeak service
                        $team_speak = [
                            'service_name' => $services_list[$index]["service_name"],
                            'display_name' => $services_list[$index]["display_name"],
                            'start_cmd' => $services_list[$index]["start_cmd"],
                            'stop_cmd' => $services_list[$index]["stop_cmd"],
                            'enable' => $services_list[$index]["enable"]
                        ];

                        // get team-speak status
                        if ($this->isProcessRunning($services_list[$index]["service_name"])) {
                            $team_speak += ['status' => 'online'];
                        } else {
                            $team_speak += ['status' => 'offline'];
                        }

                        // add team-speak array to services
                        array_push($services, $team_speak);
                    }

                    // minecraft server service
                    elseif ($services_list[$index]['service_name'] == 'minecraft') {

                        // build minecraft service
                        $minecraft = [
                            'service_name' => $services_list[$index]["service_name"],
                            'display_name' => $services_list[$index]["display_name"],
                            'start_cmd' => $services_list[$index]["start_cmd"],
                            'stop_cmd' => $services_list[$index]["stop_cmd"],
                            'enable' => $services_list[$index]["enable"]
                        ];

                        // get minecraft status
                        if ($this->isSocktOpen("127.0.0.1", "25565") == "Online") {
                            $minecraft += ['status' => 'online'];
                        }  else {
                            $minecraft += ['status' => 'offline'];
                        }

                        // add minecraft array to services
                        array_push($services, $minecraft);
                    }

                    // others services
                    else {

                        // build service array
                        $service_array = [
                            'service_name' => $services_list[$index]["service_name"],
                            'display_name' => $services_list[$index]["display_name"],
                            'start_cmd' => $services_list[$index]["start_cmd"],
                            'stop_cmd' => $services_list[$index]["stop_cmd"],
                            'enable' => $services_list[$index]["enable"]
                        ];

                        // get service status
                        if ($this->isServiceRunning($services_list[$index]["service_name"])) {
                            $service_array += ['status' => 'online'];
                        } else {
                            $service_array += ['status' => 'offline'];
                        }

                        // add service_array array to services
                        array_push($services, $service_array);
                    }
                }
            }
        } else {
            $this->logHelper->log('app-error', 'error to get services-list.json file, try check app root if file exist');
        }

        return $services;
    }

    public function isServiceRunning(string $service): bool 
    {
        // execute cmd
        $output = shell_exec("systemctl is-active $service");
        
        // check if service running
        if (trim($output) == "active") {
            return true;
        } else {
            return false;
        }
    }

    public function isScreenSessionRunning(string $session_name): bool {

        // execite cmd
        $exec = shell_exec("sudo screen -S $session_name -Q select . ; echo $?");
    
        // check if exec get output
        if ($exec == "0") {
            return true;
        } else {
            return false;
        }
    }

    public function isSocktOpen(string $ip, int $port): string {

        // default response output
        $response_output = "Offline";

        // open service socket
        $service = @fsockopen($ip, $port);

        // check is service online
        if($service >= 1) {
            $response_output = 'Online';
        }

        return $response_output;
    }

    public function isProcessRunning(string $process): bool 
    {
        // execute cmd
        exec("pgrep ".$process, $pids);
        
        // check if outputed pid
        if(empty($pids)) {
            return true;
        } else {
            return false;
        }
    }

    public function isUfwRunning(): bool 
    {
        // execute cmd
        $output = shell_exec("sudo ufw status");
    
        // check if ufw running
        if (str_starts_with($output, "Status: active")) {
            return true;
        } else {
            return false;
        }
    }
}
