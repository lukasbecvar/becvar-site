<?php

namespace App\Controller\Public;

use App\Entity\Message;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Form\ContactFormType;
use App\Manager\VisitorManager;
use App\Manager\MessagesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ContactController
 *
 * Contact controller provides contact links & contact form
 * Page to display contact information and a form that stores messages in the database
 *
 * @package App\Controller\Public
*/
class ContactController extends AbstractController
{
    private LogManager $logManager;
    private VisitorManager $visitorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private MessagesManager $messagesManager;

    public function __construct(
        LogManager $logManager,
        VisitorManager $visitorManager,
        VisitorInfoUtil $visitorInfoUtil,
        MessagesManager $messagesManager,
    ) {
        $this->logManager = $logManager;
        $this->visitorManager = $visitorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
        $this->messagesManager = $messagesManager;
    }

    /**
     * Renders the public contact page
     *
     * @param Request $request The request object
     *
     * @return Response The contact page view response
     */
    #[Route('/contact', methods: ['GET', 'POST'], name: 'public_contact')]
    public function contactPage(Request $request): Response
    {
        // init default resources
        $errorMsg = null;
        $successMsg = null;

        // get visitor ip address
        $ipAddress = $this->visitorInfoUtil->getIP();

        // handle success status
        if ($request->query->get('status') == 'ok') {
            $successMsg = 'contact.success.message';
        }

        // handle limit reached status
        if ($request->query->get('status') == 'reached') {
            $errorMsg = 'contact.error.limit.reached.message';
        }

        // handle error status
        if ($request->query->get('status') == 'ko') {
            $errorMsg = 'contact.error.ko.message';
        }

        // create message entity
        $message = new Message();

        // create register form
        $form = $this->createForm(ContactFormType::class, $message);

        // processing an HTTP request
        $form->handleRequest($request);

        // check form if submited
        if ($form->isSubmitted() && $form->isValid()) {
            // get form data
            $name = $form->get('name')->getData();
            $email = $form->get('email')->getData();
            $messageInput = $form->get('message')->getData();

            // get honeypot value
            $honeypot = $form->get('websiteIN')->getData();

            // check if values empty
            if (empty($name)) {
                $errorMsg = 'contact.error.username.empty';
            } elseif (empty($email)) {
                $errorMsg = 'contact.error.email.empty';
            } elseif (empty($messageInput)) {
                $errorMsg = 'contact.error.message.empty';
            } elseif (strlen($messageInput) > 2000) {
                $errorMsg = 'contact.error.characters.limit.reached';

            // check if honeypot is empty
            } elseif (isset($honeypot)) {
                $errorMsg = 'contact.error.blocked.message';
                $this->logManager->log(
                    name: 'message-sender',
                    value: 'message by: ' . $email . ', has been blocked: honeypot used'
                );
            } else {
                // get others data
                $visitorId = strval($this->visitorManager->getVisitorID($ipAddress));

                // check if user have unclosed messages
                if ($this->messagesManager->getMessageCountByIpAddress($ipAddress) >= 5) {
                    $this->logManager->log(
                        name: 'message-sender',
                        value: 'visitor: ' . $visitorId . ' trying send new message but he has open messages'
                    );

                    // redirect back to from & handle limit reached error status
                    return $this->redirectToRoute('public_contact', ['status' => 'reached']);
                } else {
                    // save message & get return boolean
                    $save = $this->messagesManager->saveMessage($name, $email, $messageInput, $ipAddress, $visitorId);

                    // check if message saved
                    if ($save) {
                        return $this->redirectToRoute('public_contact', ['status' => 'ok']);
                    } else {
                        return $this->redirectToRoute('public_contact', ['status' => 'ko']);
                    }
                }
            }
        }

        // render contact page
        return $this->render('public/contact.twig', [
            // contact form data
            'errorMsg' => $errorMsg,
            'successMsg' => $successMsg,
            'contactForm' => $form->createView(),

            // contact data
            'githubLink' => $_ENV['GITHUB_LINK'],
            'twitterLink' => $_ENV['TWITTER_LINK'],
            'contactEmail' => $_ENV['CONTACT_EMAIL'],
            'telegramLink' => $_ENV['TELEGRAM_LINK'],
            "linkedInLink" => $_ENV['LINKEDIN_LINK'],
            'instagramLink' => $_ENV['INSTAGRAM_LINK'],
        ]);
    }
}
