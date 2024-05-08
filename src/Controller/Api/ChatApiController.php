<?php

namespace App\Controller\Api;

use App\Util\SecurityUtil;
use App\Entity\ChatMessage;
use App\Manager\LogManager;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ChatApiController
 *
 * This controller provides API functions for saving and retrieving chat messages.
 *
 * @package App\Controller\Api
 */
class ChatApiController extends AbstractController
{
    private LogManager $logManager;
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LogManager $logManager,
        AuthManager $authManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    /**
     * API endpoint for saving a chat message.
     *
     * @param Request $request The request object.
     * @return Response Returns a Response with the status and message indicating the result of the operation.
     *
     * @throws \Exception Throws an exception if there is an error during the message save process.
     */
    #[Route('/api/chat/save/message', methods: ['POST'], name: 'api_chat_save')]
    public function saveMessage(Request $request): Response
    {
        // check if user authorized
        if (!$this->authManager->isUserLogedin()) {
            return $this->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'error to save message: only for authenticated users!'
            ], 401);
        }

        // check request type
        if (!$request->isMethod('POST')) {
            return $this->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'POST request required!'
            ], 500);
        }

        // get time data
        $day = date('d.m.Y');
        $time = date('H:i');

        // get user token
        $token = $this->authManager->getUserToken();

        // get message data
        $data = json_decode($request->getContent(), true);

        // check if message seted
        if (!isset($data['message'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'chat message not saved'
            ], 400);
        }

        // escape message (XSS protection)
        $chat_message = $this->securityUtil->escapeString($data['message']);

        if (empty(trim($chat_message))) {
            return $this->json([
                'status' => 'error',
                'message' => 'message input is empty'
            ], 400);
        }

        // encrypt message
        $chat_message = $this->securityUtil->encryptAes($chat_message);

        // init chat message entity
        $message = new ChatMessage();

        // set message data
        $message->setMessage($chat_message);
        $message->setSender($token);
        $message->setDay($day);
        $message->setTime($time);

        // save message data
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return $this->json([
                'status' => 'success',
                'message' => 'chat message saved'
            ], 200);
        } catch (\Exception $e) {
            $this->logManager->log('system-error', 'chat message save error: ' . $e->getMessage());
            return $this->errorManager->handleError('error to save message: ' . $e->getMessage(), 401);
        }
    }

    /**
     * Get chat messages.
     *
     * @return Response
     */
    #[Route('/api/chat/get/messages', methods: ['GET'], name: 'api_chat_get')]
    public function getMessages(): Response
    {
        // check if user authorized
        if (!$this->authManager->isUserLogedin()) {
            return $this->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'error to get messages: only for authenticated users!'
            ], 401);
        }

        // get max message limit
        $limit = intval($_ENV['ITEMS_PER_PAGE']);

        // get messages
        $messages = $this->entityManager->getRepository(ChatMessage::class)->findBy([], ['id' => 'DESC'], $limit);

        // sort messages reverse
        $messages = array_reverse($messages);

        $messages_data = [];

        // build message data
        foreach ($messages as $message) {
            // get sender token
            $sender = $message->getSender();

            // decrypt message
            $decrypted_message = $this->securityUtil->decryptAes($message->getMessage());

            // check if message is decrypted
            if ($decrypted_message == null) {
                $this->errorManager->handleError('Error to decrypt aes message', 500);
            }

            // build message
            $messages_data[] = [
                'id' => $message->getId(),
                'day' => $message->getDay(),
                'time' => $message->getTime(),
                'sender' => $this->authManager->getUsername($sender),
                'role' => $this->authManager->getUserRole($sender),
                'pic' => $this->authManager->getUserProfilePic($sender),
                'message' => $decrypted_message
            ];
        }

        // return messages json
        return $this->json($messages_data);
    }
}
