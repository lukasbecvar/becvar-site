<?php

namespace App\DataFixtures;

use App\Entity\Log;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class LogFixtures
 *
 * LogFixtures loads sample log data into the database.
 *
 * @package App\DataFixtures
 */
class LogFixtures extends Fixture
{
    /**
     * Load log fixtures into the database.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $logsData = [
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: HbfLDhh2c6qfOgg',
                'time' => '29.03.2024 20:35:21',
                'ip_address' => '35.237.4.214',
                'browser' => 'Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)',
                'status' => 'unreaded',
                'visitor_id' => '48',
            ],
            [
                'name' => 'internal-error',
                'value' => 'find error: An exception occurred in the driver: SQLSTATE[HY000] [2002] No such file or directory',
                'time' => '29.03.2024 20:54:03',
                'ip_address' => '2a00:1028:838e:71a6:bfd:3ae:61:cbbd',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '32',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: barnhill.maira@gmail.com, has been blocked: honeypot used',
                'time' => '31.03.2024 00:04:24',
                'ip_address' => '45.131.195.176',
                'browser' => 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:114.0) Gecko/20100101 Firefox/114.0',
                'status' => 'unreaded',
                'visitor_id' => '82',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: cDrkcZe61cg1xfi',
                'time' => '01.04.2024 14:05:59',
                'ip_address' => '35.227.62.178',
                'browser' => 'Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)',
                'status' => 'unreaded',
                'visitor_id' => '11',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: 3WgdkFcLAhKDSf9',
                'time' => '01.04.2024 15:33:11',
                'ip_address' => '2a00:1028:838e:71a6:cbde:333b:9ae2:f1b',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '130',
            ],
            [
                'name' => 'internal-error',
                'value' => 'not found error, image: wd7icA2dTKTv9vp5SseaqBPf8kiszAdQ, not found in database',
                'time' => '03.04.2024 11:47:19',
                'ip_address' => '34.148.220.118',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11.6; rv:92.0) Gecko/20100101 Firefox/92.0',
                'status' => 'unreaded',
                'visitor_id' => '193',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: Y6Ve3164NMwk0Ia',
                'time' => '03.04.2024 13:33:18',
                'ip_address' => '77.48.24.24',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '194',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: qsX2jZBnhgKlzI7',
                'time' => '03.04.2024 13:33:27',
                'ip_address' => '77.48.24.24',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '194',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: Y6Ve3164NMwk0Ia',
                'time' => '03.04.2024 13:33:33',
                'ip_address' => '77.48.24.24',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '194',
            ],
            [
                'name' => 'code-paste',
                'value' => 'visitor viewed paste: qsX2jZBnhgKlzI7',
                'time' => '03.04.2024 13:33:53',
                'ip_address' => '77.48.24.24',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '194',
            ],
            [
                'name' => 'authenticator',
                'value' => 'user: lordbecvold logged in',
                'time' => '06.04.2024 07:28:48',
                'ip_address' => '2a00:1028:838e:71a6:ec0a:1029:dc19:fee1',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '283',
            ],
            [
                'name' => 'anti-log',
                'value' => 'user: lordbecvold unset antilog',
                'time' => '06.04.2024 07:28:51',
                'ip_address' => '2a00:1028:838e:71a6:ec0a:1029:dc19:fee1',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '283',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: noreplyhere@aol.com, has been blocked: honeypot used',
                'time' => '07.04.2024 04:36:41',
                'ip_address' => '163.5.241.114',
                'browser' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Vivaldi/5.3.2679.68',
                'status' => 'unreaded',
                'visitor_id' => '305',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: lemaster.ivy33@gmail.com, has been blocked: honeypot used',
                'time' => '09.04.2024 08:19:12',
                'ip_address' => '64.64.108.41',
                'browser' => 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '365',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: christiane.costas@gmail.com, has been blocked: honeypot used',
                'time' => '10.04.2024 16:02:22',
                'ip_address' => '64.64.123.55',
                'browser' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '389',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: conolly.galen@msn.com, has been blocked: honeypot used',
                'time' => '16.04.2024 02:45:45',
                'ip_address' => '103.163.220.52',
                'browser' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '508',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: willmott.sharyn51@yahoo.com, has been blocked: honeypot used',
                'time' => '21.04.2024 18:56:53',
                'ip_address' => '93.127.170.23',
                'browser' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 OPR/89.0.4447.51',
                'status' => 'unreaded',
                'visitor_id' => '631',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: admin@charterunionfin.com, has been blocked: honeypot used',
                'time' => '25.04.2024 10:51:03',
                'ip_address' => '45.86.201.10',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '697',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: noreplyhere@aol.com, has been blocked: honeypot used',
                'time' => '25.04.2024 15:39:07',
                'ip_address' => '62.12.114.42',
                'browser' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                'status' => 'unreaded',
                'visitor_id' => '703',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: reece.levay@gmail.com, has been blocked: honeypot used',
                'time' => '28.04.2024 21:22:43',
                'ip_address' => '185.132.187.97',
                'browser' => 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:114.0) Gecko/20100101 Firefox/114.0',
                'status' => 'unreaded',
                'visitor_id' => '775',
            ],
            [
                'name' => 'message-sender',
                'value' => 'message by: tammi.gloeckner2@gmail.com, has been blocked: honeypot used',
                'time' => '30.04.2024 18:06:48',
                'ip_address' => '173.244.55.12',
                'browser' => 'Mozilla/5.0 (Linux x86_64; rv:114.0) Gecko/20100101 Firefox/114.0',
                'status' => 'unreaded',
                'visitor_id' => '814',
            ]
        ];

        // create objects with the given data
        foreach ($logsData as $logData) {
            $log = new Log();

            // set the object's properties
            $log->setName($logData['name'])
                ->setValue($logData['value'])
                ->setTime($logData['time'])
                ->setIpAddress($logData['ip_address'])
                ->setBrowser($logData['browser'])
                ->setStatus($logData['status'])
                ->setVisitorId($logData['visitor_id']);

            // persist the object
            $manager->persist($log);
        }

        // save all the objects to the database
        $manager->flush();
    }
}
