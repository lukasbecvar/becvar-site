<?php

namespace App\Util;

use App\Entity\Visitor;
use App\Helper\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitor util provides visitor info getters
*/

class VisitorUtil
{
    private $errorHelper;
    private $entityManager;

    public function __construct (
        ErrorHelper $errorHelper, 
        EntityManagerInterface $entityManager
    ) {
        $this->errorHelper = $errorHelper;
        $this->entityManager = $entityManager;
    }

    public function getIP(): ?string 
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];
      
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {
            $address = $_SERVER['REMOTE_ADDR'];
        }
        return $address;
    }

    public function getBrowser(): ?string 
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'Unknown';

        if ($agent != null) {
            $browser = $agent;
        }
            
        return $browser;
    }

    public function getOS(): ?string 
    { 
        $agent = $this->getBrowser();
        
        $os = 'Unknown OS';
        
        // OS list
        $os_array = array (
            '/windows/i'            =>  'Windows',
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server_2003',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile',
            '/SMART-TV/i'           =>  'Smart TV'
        );
        
        foreach ($os_array as $regex => $value) {

            // check if os found
            if (preg_match($regex, $agent)) {
                $os = $value;
            }
        }
        
        return $os;
    }

    public function getVisitorID(string $ip_address): ?int {

        $repository = $this->entityManager->getRepository(Visitor::class);

        // try to get visitor data
        try {
            $result = $repository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorHelper->handleError('find error: '.$e->getMessage(), 500);
        }

        if ($result === null) {
            return 0;
        } else {
            return $result->getID();
        }

    }
}
