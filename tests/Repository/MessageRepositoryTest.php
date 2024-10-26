<?php

namespace App\Tests\Repository;

use App\Entity\Message;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class MessageRepositoryTest
 *
 * Test for doctrine entity repository class
 *
 * @package App\Tests\Repository
 */
class MessageRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * Test get messages by status
     *
     * @return void
     */
    public function testGetMessagesByStatus(): void
    {
        /** @var \App\Repository\MessageRepository $messageRepository */
        $messageRepository = $this->entityManager->getRepository(Message::class);

        $status = 'open';
        $messages = $messageRepository->getMessagesByStatus($status);

        // assert result
        $this->assertIsArray($messages, 'Messages should be returned as an array');
        $this->assertNotEmpty($messages, 'Messages should not be empty');

        // assert item result
        foreach ($messages as $message) {
            $this->assertInstanceOf(Message::class, $message, 'Each item should be an instance of Message');
            $this->assertEquals($status, $message->getStatus(), 'The message status should match the filter');
        }
    }
}
