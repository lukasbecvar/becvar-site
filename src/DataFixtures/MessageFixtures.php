<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class MessageFixtures
 *
 * MessageFixtures loads sample inbox data into the database.
 *
 * @package App\DataFixtures
 */
class MessageFixtures extends Fixture
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Load inbox fixtures into the database.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // message data
        $messageData = [
            [ 'message' => 'test message 1' ],
            [ 'message' => 'test message 2' ],
            [ 'message' => 'test message 3' ],
            [ 'message' => 'test message 4' ],
            [ 'message' => 'test message 5' ]
        ];

        foreach ($messageData as $data) {
            $message = new Message();

            $message->setName('Lukáš Bečvář');
            $message->setEmail('becvarlukas99@gmail.com	');
            $message->setMessage($this->securityUtil->encryptAes($data['message']));
            $message->setTime('02.05.2024 12:21:39');
            $message->setIpAddress('172.18.0.1');
            $message->setStatus('open');
            $message->setVisitorID('1');

            // persist message fixtures
            $manager->persist($message);
        }

        // flush database changes
        $manager->flush();
    }
}
