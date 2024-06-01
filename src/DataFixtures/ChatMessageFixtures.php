<?php

namespace App\DataFixtures;

use App\Util\SecurityUtil;
use App\Entity\ChatMessage;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class ChatMessageFixtures
 *
 * ChatMessageFixtures loads sample chat data into the database.
 *
 * @package App\DataFixtures
 */
class ChatMessageFixtures extends Fixture
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Load chat fixtures into the database.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $chatMessages = [
            [
                'message' => 'testing message 1',
                'sender' => 1,
                'day' => '03.05.2024',
                'time' => '11:56'
            ],
            [
                'message' => 'testing message 2',
                'sender' => 2,
                'day' => '01.05.2024',
                'time' => '09:56'
            ],
            [
                'message' => 'testing message 3',
                'sender' => 3,
                'day' => '09.05.2024',
                'time' => '10:56'
            ],
            [
                'message' => 'testing message 4',
                'sender' => 4,
                'day' => '03.05.2024',
                'time' => '13:56'
            ]
        ];

        // create new chat messages
        foreach ($chatMessages as $message) {
            $newMessage = new ChatMessage();

            // set message properties
            $newMessage->setMessage($this->securityUtil->encryptAes($message['message']))
                ->setSender($message['sender'])
                ->setDay($message['day'])
                ->setTime($message['time']);

            // persist the entity
            $manager->persist($newMessage);
        }

        // save all the fixtures
        $manager->flush();
    }
}
