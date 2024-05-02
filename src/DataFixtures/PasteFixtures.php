<?php

namespace App\DataFixtures;

use App\Entity\Paste;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class PasteFixtures
 * 
 * PasteFixtures loads sample Paste data into the database.
 * 
 * @package App\DataFixtures
 */
class PasteFixtures extends Fixture
{
    private SecurityUtil $securityUtil;
        
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Load paste fixtures into the database.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // paste data
        $paste_data = [
            [
                'name' => 'h7T7QxJdxycfaqc',
                'content' => 'testing paste',
                'time' => '02.05.2024 12:11:09',
            ],
            [
                'name' => 'izDXZvrKqSAjJlG',
                'content' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integer rutrum, orci vestibulum ullamcorper ultricies, lacus quam ultricies odio, vitae placerat pede sem sit amet enim. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Aliquam erat volutpat. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Integer tempor. Integer rutrum, orci vestibulum ullamcorper ultricies, lacus quam ultricies odio, vitae placerat pede sem sit amet enim. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Sed vel lectus. Donec odio tempus molestie, porttitor ut, iaculis quis, sem. Duis pulvinar. Vivamus porttitor turpis ac leo. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Nam quis nulla. Nullam at arcu a est sollicitudin euismod.',
                'time' => '02.05.2024 12:11:17',
            ],
        ];

        // persist paste fixtures
        foreach ($paste_data as $data) {
            $paste = new Paste();
            $paste->setName($data['name']);
            $paste->setContent($this->securityUtil->encryptAes($data['content']));
            $paste->setTime($data['time']);
            $manager->persist($paste);
        }

        // flush database changes
        $manager->flush();
    }
}
