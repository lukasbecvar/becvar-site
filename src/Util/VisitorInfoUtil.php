<?php

namespace App\Util;

use App\Entity\Visitor;
use Detection\MobileDetect;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;

/*
    Visitorinfo util provides visitor info getters
*/

class VisitorInfoUtil
{
    private $siteUtil;
    private $errorManager;
    private $entityManager;

    public function __construct (
        SiteUtil $siteUtil,
        ErrorManager $errorManager, 
        EntityManagerInterface $entityManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
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
            '/windows nt 5.2/i'     =>  'Windows Server_2003',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/win16/i'              =>  'Windows 3.11',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/blackberry/i'         =>  'BlackBerry',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/SMART-TV/i'           =>  'Smart TV',
            '/windows/i'            =>  'Windows',
            '/iphone/i'             =>  'Mac IOS',
            '/android/i'            =>  'Android',
            '/webos/i'              =>  'Mobile',
            '/ubuntu/i'             =>  'Ubuntu',
            '/linux/i'              =>  'Linux',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad'
        );
        
        foreach ($os_array as $regex => $value) {

            // check if os found
            if (preg_match($regex, $agent)) {
                $os = $value;
            }
        }
        
        return $os;
    }

    public function getVisitorRepository(string $ip_address): ?object 
    {
        // get visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        // try to find visitor in database
        try {
            $result = $visitorRepository->findOneBy(['ip_address' => $ip_address]);
        } catch (\Exception $e) {
            $this->errorManager->handleError('find error: '.$e->getMessage(), 500);
        }

        // return result
        if ($result !== null) {
            return $result;
        } else {
            return null;
        }
    }

    public function getVisitorID(string $ip_address): ?int 
    {
        // try to get visitor data
        $result = $this->getVisitorRepository($ip_address);

        if ($result === null) {
            return 0;
        } else {
            return $result->getID();
        }

    }

    public function getLocation(string $ip_address): ?string
    {
        $location = null;

        // check if site running on localhost
        if ($this->siteUtil->isRunningLocalhost()) {
            return 'localhost';
        } else {
 
            try {
                $geoplugin_url = $_ENV['GEOPLUGIN_URL'];

                // get data from geoplugin
                $geoplugin_data = file_get_contents($geoplugin_url.'/json.gp?ip='.$ip_address);

                // decode data
                $details = json_decode($geoplugin_data);
        
                // get country
                $country = $details->geoplugin_countryCode;

                // check if city name defined
                if (!empty(explode('/', $details->geoplugin_timezone)[1])) {
                        
                    // get city name from timezone (explode /)
                    $city = explode('/', $details->geoplugin_timezone)[1];
                } else {
                    $city = null;
                }
            } catch (\Exception) {

                // set null if data not getted
                $country = null;
                $city = null;
            }   
        }

        // empty set to null
        if (empty($country)) {
            $country = null;
        }
        if (empty($city)) {
            $city = null;
        }

        // final return
        if  ($country == null or $city == null) {
            $location = 'Unknown';
        } else {
            $location = $country.'/'.$city;
        }

        return $location;
    }

    public function updateVisitorEmail(string $ip_address, string $email): void
    {
        $visitor = $this->getVisitorRepository($ip_address);

        // check visitor found
        if ($visitor !== null) {
            $visitor->setEmail($email);

            // try to update email
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->errorManager->handleError('flush error: '.$e->getMessage(), 500);
            }           
        }
    }

    public function isMobile(): bool {
        $detect = new MobileDetect();

        // check if mobile device
        if ($detect->isMobile()) {
            return true;
        } else {
            return false;
        }
    }
}
