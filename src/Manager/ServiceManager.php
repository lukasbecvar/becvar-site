<?php

namespace App\Manager;

use App\Helper\ErrorHelper;
use App\Helper\LogHelper;
use App\Util\JsonUtil;

/*
    Service manager provides all services methods (start, stop, status)
*/

class ServiceManager
{
    private $jsonUtil;
    private $logHelper;
    private $errorHelper;
    private $authManager;

    public function __construct(
        JsonUtil $jsonUtil, 
        LogHelper $logHelper,
        ErrorHelper $errorHelper,
        AuthManager $authManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->authManager = $authManager;
    }

    public function getServicesJson() {
        return $services_list = $this->jsonUtil->getJson(__DIR__.'/../../services.json');
    }

    public function isServicesListExist(): bool 
    {
        // check if list is null
        if ($this->getServicesJson() != null) {
            return true;
        } else {
            return false;
        }
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

    public function executeCommand($command): void {

        // execute command
        shell_exec('sudo '.$command);
    }

    public function emergencyShutdown() {
        $this->logHelper->log('action-runner', $this->authManager->getUsername().' initiated emergency-shutdown');
        dd('shtudown');
    }

    public function getServices(): ?array 
    {
        $services_list = $this->getServicesJson();
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
                        if (($this->isSocktOpen("127.0.0.1", "25565") == "Offline") && ($this->isScreenSessionRunning("minecraft"))) {
                            $minecraft += ['status' => 'starting'];
                        } elseif ($this->isSocktOpen("127.0.0.1", "25565") == "Online") {
                            $minecraft += ['status' => 'online'];
                        } else {
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

    public function runAction(string $service_name, string $action): void
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            $services_list = $this->getServicesJson();
            $username = $this->authManager->getUsername();

            // check if action is emergency shutdown
            if ($service_name == 'emergency_cnA1OI5jBL' && $action == 'shutdown_MEjP9bqXF7') {
                $this->emergencyShutdown();
            } else {

                // start action
                if ($action == 'start') {
                    $command = $services_list[$service_name]['start_cmd'];
                    $this->logHelper->log('action-runner', $username.' started '.$service_name);

                // stop action
                } elseif ($action == 'stop') {
                    $command = $services_list[$service_name]['stop_cmd'];
                    $this->logHelper->log('action-runner', $username.' stoped '.$service_name);
                } else {
                    $this->errorHelper->handleError('action runner error: action: '.$action.' not supported', 400);
                }

                // executed final command
                $this->executeCommand($command);
            }
        } else {
            $this->errorHelper->handleError('error action runner is only for authentificated users', 401);
        }
    }
}
