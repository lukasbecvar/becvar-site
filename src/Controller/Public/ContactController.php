<?php

namespace App\Controller\Public;

use App\Entity\Message;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Util\VisitorInfoUtil;
use App\Form\ContactFormType;
use App\Manager\AuthManager;
use App\Manager\MessagesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Contact controller provides contact links & contact form
    Page to display contact information and a form that stores messages in the database
*/

class ContactController extends AbstractController
{   
    private LogManager $logManager;
    private AuthManager $authManager;
    private SecurityUtil $securityUtil;
    private VisitorInfoUtil $visitorInfoUtil;
    private MessagesManager $messagesManager;

    public function __construct(
        LogManager $logManager, 
        AuthManager $authManager,
        SecurityUtil $securityUtil, 
        VisitorInfoUtil $visitorInfoUtil,
        MessagesManager $messagesManager,
    ) {
        $this->logManager = $logManager;
        $this->authManager = $authManager;
        $this->securityUtil = $securityUtil;
        $this->messagesManager = $messagesManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/contact', name: 'public_contact')]
    public function contactPage(Request $request): Response
    {
        // default msg state
        $error_msg = null;
        $success_msg = null;

        // get visitor ip address
        $ip_address = $this->visitorInfoUtil->getIP();

        // handle success status
        if ($request->query->get('status') == 'ok') {
            $success_msg = 'contact.success.message';
        }

        // handle limit reached status
        if ($request->query->get('status') == 'reached') {
            $error_msg = 'contact.error.limit.reached.message';
        }

        // handle error status
        if ($request->query->get('status') == 'ko') {
            $error_msg = 'contact.error.ko.message';
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
            $message_input = $form->get('message')->getData();

            // get honeypot value 
            $honeypot = $form->get('websiteIN')->getData();

            // check if values empty
            if (empty($name)) {
                $error_msg = 'contact.error.username.empty';
            } else if (empty($email)) {
                $error_msg = 'contact.error.email.empty';
            } else if (empty($message_input)) {
                $error_msg = 'contact.error.message.empty';
            } else if (strlen($message_input) > 2000) {
                $error_msg = 'contact.error.characters.limit.reached';

            // check if honeypot is empty
            } else if (isset($honeypot)) {
                $error_msg = 'contact.error.blocked.message';
                $this->logManager->log('message-sender', 'message: '.$message_input.', has been blocked: honeypot used');
            } else {

                // escape values (XSS protection)
                $name = $this->securityUtil->escapeString($name);
                $email = $this->securityUtil->escapeString($email);
                $message_input = $this->securityUtil->escapeString($message_input);

                // get others data
                $visitor_id = strval($this->visitorInfoUtil->getVisitorID($ip_address));

                // check if user have unclosed messages
                if ($this->messagesManager->getMessageCountByIpAddress($ip_address) >= 5) {
                    $this->logManager->log('message-sender', 'visitor: '.$visitor_id.' trying send new message but he has open messages');

                    // redirect back to from & handle limit reached error status
                    return $this->redirectToRoute('public_contact', ['status' => 'reached']);
                } else {

                    // save message & get return boolean
                    $save = $this->messagesManager->saveMessage($name, $email, $message_input, $ip_address, $visitor_id);

                    if ($save) {
                        return $this->redirectToRoute('public_contact', ['status' => 'ok']);
                    } else {
                        return $this->redirectToRoute('public_contact', ['status' => 'ko']);
                    }
                }
            }
        }

        // render contact page
        return $this->render('public/contact.html.twig', [
            'user_logged' => $this->authManager->isUserLogedin(),
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'contact_form' => $form->createView(),
            'error_msg' => $error_msg,
            'success_msg' => $success_msg
        ]);
    }
}
