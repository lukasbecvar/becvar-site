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
     *
     * @throws \Exception Throws an exception if there is an error during the message save process.
     *
     * @return Response Returns a Response with the status and message indicating the result of the operation.
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

        // get user repo
        $userRepo = $this->authManager->getUserRepository(['token' => $token]);

        // get user id
        $userId = $userRepo->getId();

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
        $chatMessage = $this->securityUtil->escapeString($data['message']);

        // check if message is empty
        if (empty($chatMessage)) {
            return $this->json([
                'status' => 'error',
                'message' => 'message input is empty'
            ], 400);
        }

        // encrypt message
        $chatMessage = $this->securityUtil->encryptAes($chatMessage);

        // init chat message entity
        $message = new ChatMessage();

        // set message data
        $message->setMessage($chatMessage)
            ->setSender($userId)
            ->setDay($day)
            ->setTime($time);

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
     * @return Response object with messages data.
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

        // response messages array
        $messagesData = [];

        // build message data
        foreach ($messages as $message) {
            // get sender token
            $sender = $message->getSender();

            // get sender data
            $sender = $this->authManager->getUserRepository(['id' => $sender]);

            // check if sender exists
            if ($sender != null) {
                // get sender token
                $token = $sender->getToken();

                // decrypt message
                $decryptedMessage = $this->securityUtil->decryptAes($message->getMessage());

                // check if message is decrypted
                if ($decryptedMessage == null) {
                    $this->errorManager->handleError('Error to decrypt aes message', 500);
                }

                // build message
                $messagesData[] = [
                    'id' => $message->getId(),
                    'day' => $message->getDay(),
                    'time' => $message->getTime(),
                    'sender' => $this->authManager->getUsername($token),
                    'role' => $this->authManager->getUserRole($token),
                    'pic' => $this->authManager->getUserProfilePic($token),
                    'message' => $decryptedMessage
                ];
            }
        }

        // return messages json
        return $this->json($messagesData);
    }
}
