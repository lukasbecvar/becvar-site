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

/*
    This controller provides API function: save, get chat messages
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

    #[Route('/api/chat/save/message', name: 'api_chat_save')]
    public function saveMessage(Request $request): Response
    {
        // check if user loggedin
        if ($this->authManager->isUserLogedin()) {

            // get current day
            $day = date('d.m.Y');

            // get time
            $time = date('H:i');

            // get token
            $token = $this->authManager->getUserToken();

            // get message data
            $data = json_decode($request->getContent(), true);

            // check if message seted
            if (isset($data['message'])) {

                // escape message (XSS protection)
                $chat_message = $this->securityUtil->escapeString($data['message']);
                
                // encrypt message
                $chat_message = $this->securityUtil->encrypt_aes($chat_message);

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
                } catch (\Exception $e) {
                    $this->logManager->log('system-error', 'chat message save error: '.$e->getMessage());
                    return $this->errorManager->handleError('error to save message: ' . $e->getMessage(), 401);
                }
                return $this->json([
                    'status' => 'success',
                    'message' => 'chat message saved'
                ], 200);
            } else {
                return $this->json([
                    'status' => 'error',
                    'message' => 'chat message not saved'
                ], 400);
            }
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'error to save message: only for authenticated users!'
            ], 401);
        }
    }

    #[Route('/api/chat/get/messages', name: 'api_chat_get')]
    public function getMessages(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            $messageData = [];
            
            // get max message limit
            $limit = intval($_ENV['ITEMS_PER_PAGE']);

            // get messages
            $messages = $this->entityManager->getRepository(ChatMessage::class)->findBy([], ['id' => 'DESC'], $limit);

            // sort messages reverse
            $messages = array_reverse($messages);

            // build message data
            foreach ($messages as $message) {

                // get sender token
                $sender = $message->getSender();

                // build message
                $messageData[] = [
                    'id' => $message->getId(),
                    'day' => $message->getDay(),
                    'time' => $message->getTime(),
                    'sender' => $this->authManager->getUsername($sender),
                    'role' => $this->authManager->getUserRole($sender),
                    'pic' => $this->authManager->getUserProfilePic($sender),
                    'message' => $this->securityUtil->decrypt_aes($message->getMessage())
                ];
            }
    
            // return messages json
            return $this->json($messageData);
        } else {
            return $this->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'error to get messages: only for authenticated users!'
            ], 401);
        }
    }
}
