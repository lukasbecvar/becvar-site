<?php

namespace App\Util;

use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;

/*
    Dashboard util provides admin info getters (count, database, os, etc)
*/

class DashboardUtil
{
    private $jsonUtil;
    private $errorManager;
    private $entityManager;
    
    public function __construct(
        JsonUtil $jsonUtil,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->jsonUtil = $jsonUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    public function getDatabaseEntityCount(object $entity, array $search = null): int 
    {
        $result = null;
        $repository = $this->entityManager->getRepository($entity::class);

        // find visitor in database
        try {
            if ($search == null) {
                $result = $repository->findAll();
            } else {
                $result = $repository->findBy($search);
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        return count($result);
    }

    public function getHostUptime(): string 
    {
        $ut = strtok(exec('cat /proc/uptime'), '.');
        $days = sprintf('%2d', ($ut/(3600*24)));
        $hours = sprintf('%2d', (($ut % (3600*24))/3600));
        $min = sprintf('%2d', ($ut % (3600*24) % 3600)/60);

        // format output
        return 'Days: '.$days.', Hours: '.$hours.', Min: '.$min;
    }

    public function getCpuUsage(): float 
    {
        $load = 100;
        $loads = sys_getloadavg();
        $core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
        $load = round($loads[0]/(intval($core_nums) + 1)*100, 2);
        
        if ($load > 100) {
            $load = 100;
        } else {
            $load = $load;
        }

        return $load;
    }

    public function getRamUsage(): array 
    {
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

    public function getDriveUsage(): ?string 
    {
        try {
            return exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
        } catch (\Exception $e) {
            return $this->errorManager->handleError('error to get drive usage: '.$e->getMessage(), 500);
        }
    }

    public function getSoftwareInfo(): array 
    {
        $softwares = array();
        $software = array();
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

    public function isWebUserSudo(): bool 
    {
        // testing sudo exec
        $exec = exec('sudo echo test');

        // count output length
        $len = strlen($exec);

        // check if length is valid
        if ($len == 4) {
            return true;
        } else {
            return false;
        }
   }

    public function isSystemLinux(): bool 
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'lin') {
            return true;
        } else {
            return false;
        }       
    }

    public function getWebUsername(): ?string 
    {
        try {
            return exec('whoami');
        } catch (\Exception $e) {
            return $this->errorManager->handleError('error to get web username: '.$e->getMessage(), 500);
        }
    }

    public function isBrowserListFound(): bool 
    {
       // check if list is null
       if ($this->jsonUtil->getJson(__DIR__.'/../../browser-list.json') != null) {
           return true;
       } else {
           return false;
       }
   }
}
