<?php

namespace App\Manager;

use App\Util\JsonUtil;

/*
    Service manager provides all services methods (start, stop, status)
*/

class ServiceManager
{
    private $jsonUtil;
    private $logManager;
    private $authManager;
    private $errorManager;

    public function __construct(
        JsonUtil $jsonUtil, 
        LogManager $logManager,
        AuthManager $authManager,
        ErrorManager $errorManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
    }

    public function getServices(): ?array 
    {
        $services_list = $this->getServicesJson();
        $services = [];  

        // check if services list load valid
        if ($services_list != null) {
           
            // execute separate service row
            foreach ($services_list as $value) {
                
                // check if service is enabled
                if ($value['enable']) {

                    // build service array
                    $service_array = [
                        'service_name' => $value['service_name'],
                        'display_name' => $value['display_name'],
                        'enable' => $value['enable']
                    ];

                    // get service status
                    if ($this->isServiceRunning($value['service_name'])) {
                        $service_array += ['status' => 'online'];
                    } else {
                        $service_array += ['status' => 'offline'];
                    }

                    // add service_array array to services
                    array_push($services, $service_array);
                }
            }
        } else {
            $this->logManager->log('app-error', 'error to get services-list.json file, try check app root if file exist');
        }

        return $services;
    }

    public function runAction(string $service_name, string $action): void
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            // check if action is emergency shutdown
            if ($service_name == 'emergency_cnA1OI5jBL' && $action == 'shutdown_MEjP9bqXF7') {
                $this->emergencyShutdown();
            
            } elseif ($service_name == 'ufw') {
                $command = 'sudo ufw '.$action;
            } else {
                // build action
                $command = 'sudo systemctl '.$action.' '.$service_name;
            }

            // log action
            $this->logManager->log('action-runner', $this->authManager->getUsername().' '.$action.'ed '.$service_name);

            // executed final command
            $this->executeCommand($command);
        } else {
            $this->errorManager->handleError('error action runner is only for authentificated users', 401);
        }
    }

    public function isServicesListExist(): bool 
    {
        if ($this->getServicesJson() != null) {
            return true;
        } else {
            return false;
        }
    }

    public function isServiceInstalled(string $service_name): bool
    {
        exec('dpkg -l | grep '.escapeshellarg($service_name), $output, $returnCode);
        
        if ($returnCode === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isServiceRunning(string $service): bool 
    {
        $output = shell_exec('systemctl is-active '.$service);
        
        // check if service running
        if (trim($output) == 'active') {
            return true;
        } else {
            return false;
        }
    }
    
    public function isSocktOpen(string $ip, int $port): string 
    {
        // default response output
        $response_output = 'Offline';

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
        exec('pgrep '.$process, $pids);
        
        // check if outputed pid
        if(empty($pids)) {
            return true;
        } else {
            return false;
        }
    }

    public function isUfwRunning(): bool 
    {
        try {
            // execute cmd
            $output = shell_exec('sudo ufw status');
    
            // check if ufw running
            if (str_starts_with($output, 'Status: active')) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return $this->errorManager->handleError('error to get ufw status'.$e->getMessage(), 500);
        }
    }
    
    public function executeCommand($command): void 
    {
        try {
            shell_exec($command);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to executed command: '.$e->getMessage(), 500);
        }
    }

    public function emergencyShutdown(): void
    {
        $this->logManager->log('action-runner', $this->authManager->getUsername().' initiated emergency-shutdown');
        $this->executeCommand('sudo poweroff');
    }

    public function getServicesJson(): ?array
    {
        return $this->jsonUtil->getJson(__DIR__.'/../../services.json');
    }
}
