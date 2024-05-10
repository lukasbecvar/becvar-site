<?php

namespace App\Util;

use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DashboardUtil
 *
 * DashboardUtil provides various utilities for gathering information about the server and environment.
 *
 * @package App\Util
 */
class DashboardUtil
{
    private JsonUtil $jsonUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(JsonUtil $jsonUtil, ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->jsonUtil = $jsonUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Get the count of entities in the database.
     *
     * @param object $entity The entity class.
     * @param array<string,mixed>|null $search Additional search criteria.
     *
     * @return int The count of entities.
     */
    public function getDatabaseEntityCount(object $entity, array $search = null): int
    {
        $result = null;

        // get entity repository
        $repository = $this->entityManager->getRepository($entity::class);

        // find visitor in database
        try {
            // check if search not used (search all)
            if ($search == null) {
                $result = $repository->findAll();
            } else {
                $result = $repository->findBy($search);
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: ' . $e->getMessage(), 500);
        }

        return count($result);
    }

    /**
     * Get the host uptime.
     *
     * @return string The formatted host uptime.
     */
    public function getHostUptime(): string
    {
        // get host uptime
        $up_time = strtok(exec('cat /proc/uptime'), '.');

        // get uptime values
        $days = sprintf('%2d', ($up_time / (3600 * 24)));
        $hours = sprintf('%2d', (($up_time % (3600 * 24)) / 3600));
        $min = sprintf('%2d', ($up_time % (3600 * 24) % 3600) / 60);

        // format output
        return 'Days: ' . $days . ', Hours: ' . $hours . ', Min: ' . $min;
    }

    /**
     * Get the CPU usage percentage.
     *
     * @return float The CPU usage percentage.
     */
    public function getCpuUsage(): float
    {
        $load = 100;
        $loads = sys_getloadavg();
        $coreNums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
        $load = round($loads[0] / (intval($coreNums) + 1) * 100, 2);

        // overload fix
        if ($load > 100) {
            $load = 100;
        } else {
            $load = $load;
        }

        return $load;
    }

    /**
     * Get the RAM usage information.
     *
     * @return array<string, string> An array containing RAM usage information.
     */
    public function getRamUsage(): array
    {
        exec('cat /proc/meminfo', $memoryRaw);
        $memoryFree = 0;
        $memoryTotal = 0;
        $memoryUsed = 0;
        for ($i = 0; $i < count($memoryRaw); $i++) {
            if (strstr($memoryRaw[$i], 'MemTotal')) {
                $memoryTotal = filter_var($memoryRaw[$i], FILTER_SANITIZE_NUMBER_INT);
                $memoryTotal = $memoryTotal / 1048576;
            }
            if (strstr($memoryRaw[$i], 'MemFree')) {
                $memoryFree = filter_var($memoryRaw[$i], FILTER_SANITIZE_NUMBER_INT);
                $memoryFree = $memoryFree / 1048576;
            }
        }
        $memoryUsed = $memoryTotal - $memoryFree;
        return array(
            'used'  =>  number_format($memoryUsed, 2),
            'free'  =>  number_format($memoryFree, 2),
            'total' =>  number_format($memoryTotal, 2)
        );
    }

    /**
     * Get the drive usage percentage.
     *
     * @return string|null The drive usage percentage or null on error.
     */
    public function getDriveUsage(): ?string
    {
        try {
            return exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get drive usage: ' . $e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Get information about installed software packages and the Linux distribution.
     *
     * @return array<string, array<string, string>> An array containing information about installed software packages and the Linux distribution.
     */
    public function getSoftwareInfo(): array
    {
        $softwares = array();
        $software = array();
        $softwareKey = '';
        $distro = array();
        exec('rpm -qai | grep "Name        :\|Version     :\|Release     :\|Install Date:\|Group       :\|Size        :"', $softwareRaw);
        for ($i = 0; $i < count($softwareRaw); $i++) {
            preg_match_all('/(?P<name1>.+): (?P<val1>.+) (?P<name2>.+): (?P<val2>.+)/', $softwareRaw[$i], $matches);
            if (empty($matches['name1'])) {
                continue;
            }
            if (trim($matches['name1'][0]) == 'Name') {
                $softwareKey = strtolower(trim(str_replace(array('-', 'Build', 'Source'), array('_', '', ''), $matches['val1'][0])));
            }
            $softwares[$softwareKey][strtolower(str_replace(' ', '_', trim($matches['name1'][0])))] = trim(str_replace(array('Build', 'Source'), '', $matches['val1'][0]));
            $softwares[$softwareKey][strtolower(str_replace(' ', '_', trim($matches['name2'][0])))] = trim(str_replace(array('Build', 'Source'), '', $matches['val2'][0]));
        }
        ksort($softwares);
        foreach ($softwares as $s) {
            $software[] = $s;
        }
        exec('uname -mrs', $distroRaw);
        exec('cat /etc/*-release', $distroNameRaw);
        $distroParts = explode(' ', $distroRaw[0]);
        $distro['operating_system'] = $distroNameRaw[0];
        $distro['kernal_version'] = $distroParts[0] . ' ' . $distroParts[1];
        $distro['kernal_arch'] = $distroParts[2];
        return array(
            'packages'  => $software,
            'distro'    => $distro
        );
    }

    /**
     * Check if the web user has sudo privileges.
     *
     * @return bool True if the web user has sudo privileges, false otherwise.
     */
    public function isWebUserSudo(): bool
    {
        // testing sudo exec
        $exec = exec('sudo echo test');

        // count output length
        $len = strlen($exec);

        // check if length is valid
        if ($len == 4) {
            return true;
        }

        return false;
    }

    /**
     * Check if the system is running Linux.
     *
     * @return bool True if the system is running Linux, false otherwise.
     */
    public function isSystemLinux(): bool
    {
        // check if system is linux
        if (strtolower(substr(PHP_OS, 0, 3)) == 'lin') {
            return true;
        }

        return false;
    }

    /**
     * Get the web username.
     *
     * @return string|null The web username or null on error.
     */
    public function getWebUsername(): ?string
    {
        try {
            return exec('whoami');
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get web username: ' . $e->getMessage(), 500);
            return null;
        }
    }

    /**
     * Check if the browser list is found.
     *
     * @return bool True if the browser list is found, false otherwise.
     */
    public function isBrowserListFound(): bool
    {
        // check if list is found
        if ($this->jsonUtil->getJson(__DIR__ . '/../../config/becwork/browser-list.json') != null) {
            return true;
        }

        return false;
    }
}
