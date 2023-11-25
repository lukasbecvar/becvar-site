<?php

namespace App\Manager;

use App\Util\JsonUtil;

/**
 * Service manager provides all services methods (start, stop, status)
*/
class ServiceManager
{
    /** * @var JsonUtil */
    private JsonUtil $jsonUtil;
    
    /** * @var LogManager */
    private LogManager $logManager;
    
    /** * @var AuthManager */
    private AuthManager $authManager;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /**
     * ServiceManager constructor.
     *
     * @param JsonUtil     $jsonUtil
     * @param LogManager   $logManager
     * @param AuthManager  $authManager
     * @param ErrorManager $errorManager
     */
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

    /**
     * Gets the list of services.
     *
     * @return array|null
     */
    public function getServices(): ?array 
    {
        // get services list from services.json 
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

    /**
     * Runs an action on a specified service.
     *
     * @param string $service_name
     * @param string $action
     *
     * @return void
     */
    public function runAction(string $service_name, string $action): void
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {

            $command = null;

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

    /**
     * Checks if a service is installed.
     *
     * @param string $service_name
     *
     * @return bool
     */
    public function isServiceInstalled(string $service_name): bool
    {
        exec('dpkg -l | grep '.escapeshellarg($service_name), $output, $returnCode);
        
        if ($returnCode === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a service is running.
     *
     * @param string $service
     *
     * @return bool
     */
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
    
    /**
     * Checks if a socket is open.
     *
     * @param string $ip
     * @param int $port
     *
     * @return string
     */
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

    /**
     * Checks if a process is running.
     *
     * @param string $process
     *
     * @return bool
     */
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

    /**
     * Checks if UFW (Uncomplicated Firewall) is running.
     *
     * @return bool
     */
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
            $this->errorManager->handleError('error to get ufw status'.$e->getMessage(), 500);
            return false;
        }
    }
    
    /**
     * Checks if the services list file exists.
     *
     * @return bool
     */
    public function isServicesListExist(): bool 
    {
        // check if services list exist
        if ($this->getServicesJson() != null) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Executes a command.
     *
     * @param string $command
     *
     * @return void
     */
    public function executeCommand($command): void 
    {
        try {
            shell_exec($command);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to executed command: '.$e->getMessage(), 500);
        }
    }

    /**
     * Initiates an emergency shutdown.
     *
     * @return void
     */
    public function emergencyShutdown(): void
    {
        $this->logManager->log('action-runner', $this->authManager->getUsername().' initiated emergency-shutdown');
        $this->executeCommand('sudo poweroff');
    }

    /**
     * Gets the services list from the services.json file.
     *
     * @return array|null
     */
    public function getServicesJson(): ?array
    {
        return $this->jsonUtil->getJson(__DIR__.'/../../services.json');
    }
}
