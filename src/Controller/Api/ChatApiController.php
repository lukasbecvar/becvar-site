<?php

namespace App\Controller\Api;

use App\Util\SecurityUtil;
use App\Entity\ChatMessage;
use App\Manager\AuthManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    This controller provides API function: save, get chat messages
*/

class ChatApiController extends AbstractController
{
    private AuthManager $authManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AuthManager $authManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
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

            // get username
            $username = $this->authManager->getUsername();

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
                $message->setSender($username);
                $message->setDay($day);
                $message->setTime($time);

                // save message data
                try {
                    $this->entityManager->persist($message);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->errorManager->handleError('error to save message: ' . $e->getMessage(), 401);
                    return new RedirectResponse('/');
                }

                return new JsonResponse(['status' => 'message saved']);
            } else {
                return new JsonResponse(['status' => 'message not saved'], 400);
            }
        } else {
            $this->errorManager->handleError('error to save message: only for authenticated users!', 401);
            return new RedirectResponse('/');
        }
    }

    #[Route('/api/chat/get/messages', name: 'api_chat_get')]
    public function getMessages(): Response
    {
        // check if user logged in
        if ($this->authManager->isUserLogedin()) {
            $messageData = [];
    
            // get messages
            $messages = $this->entityManager->getRepository(ChatMessage::class)->findAll();
    
            // build message data
            foreach ($messages as $message) {
                $messageData[] = [
                    'id' => $message->getId(),
                    'day' => $message->getDay(),
                    'time' => $message->getTime(),
                    'sender' => $message->getSender(),
                    'message' => $this->securityUtil->decrypt_aes($message->getMessage())
                ];
            }
    
            // return messages json
            return new JsonResponse($messageData);
        } else {
            $this->errorManager->handleError('error to get messages: only for authenticated users!', 401);
            return new RedirectResponse('/');
        }
    }
}
