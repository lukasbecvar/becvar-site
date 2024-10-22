<?php

namespace App\DataFixtures;

use App\Entity\Visitor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class VisitorFixtures
 *
 * VisitorFixtures loads sample visitor data into the database
 *
 * @package App\DataFixtures
 */
class VisitorFixtures extends Fixture
{
    /**
     * User agents for different browsers
     *
     * @var array<string>
     */
    private $browsers = [
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36',
        'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.6367.91 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)'
    ];

    /**
     * Operating systems list
     *
     * @var array<string>
     */
    private $os = [
        'Windows',
        'Linux',
        'OSX'
    ];

    /**
     * Countries list
     *
     * @var array<string>
     */
    private $county = [
        'CZ',
        'US',
        'NL'
    ];

    /**
     * Cities list
     *
     * @var array<string>
     */
    private $city = [
        'Amsterdam',
        'Singapore',
        'Prague'
    ];

    /**
     * Load visitor fixtures into the database
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            // build new visitor entity
            $visitor = new Visitor();

            // set visitor entity data
            $visitor->setFirstVisit(new \DateTime('2023-12-01 16:33:55'))
                ->setLastVisit(new \DateTime('2023-12-01 16:33:55'))
                ->setBrowser($this->browsers[array_rand($this->browsers)])
                ->setOs($this->os[array_rand($this->os)])
                ->setCity($this->city[array_rand($this->city)])
                ->setCountry($this->county[array_rand($this->county)])
                ->setIpAddress('192.168.1.' . $i)
                ->setBannedStatus($i % 2 === 0 ? true : false)
                ->setBanReason($i % 2 === 0 ? 'reason for ban' : 'non-banned')
                ->setBannedTime(null)
                ->setEmail($i % 2 === 0 ? 'unknown' : 'visitor' . $i . '@example.com');

            // persist new visitor entity
            $manager->persist($visitor);
        }

        // flush testing visitors data
        $manager->flush();
    }
}
