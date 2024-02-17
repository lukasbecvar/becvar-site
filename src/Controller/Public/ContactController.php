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
    /**
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * @var VisitorManager
     * Instance of the VisitorManager for handling visitor-related functionality.
     */
    private VisitorManager $visitorManager;

    /**
     * @var VisitorInfoUtil
     * Instance of the VisitorInfoUtil for handling visitor information-related utilities.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * @var MessagesManager
     * Instance of the MessagesManager for handling messages-related functionality.
     */
    private MessagesManager $messagesManager;

    /**
     * ContactController constructor.
     *
     * @param LogManager      $logManager      
     * @param VisitorManager  $visitorManager  
     * @param VisitorInfoUtil $visitorInfoUtil
     * @param MessagesManager $messagesManager 
     */
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
     * Renders the public contact page.
     *
     * @param Request $request The HTTP request.
     * @return Response The response containing the rendered contact page.
     */
    #[Route('/contact', methods: ['GET', 'POST'], name: 'public_contact')]
    public function contactPage(Request $request): Response
    {
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
                $this->logManager->log('message-sender', 'message by: '.$email.', has been blocked: honeypot used');
            } else {

                // get others data
                $visitor_id = strval($this->visitorManager->getVisitorID($ip_address));

                // check if user have unclosed messages
                if ($this->messagesManager->getMessageCountByIpAddress($ip_address) >= 5) {
                    $this->logManager->log('message-sender', 'visitor: '.$visitor_id.' trying send new message but he has open messages');

                    // redirect back to from & handle limit reached error status
                    return $this->redirectToRoute('public_contact', ['status' => 'reached']);
                } else {

                    // save message & get return boolean
                    $save = $this->messagesManager->saveMessage($name, $email, $message_input, $ip_address, $visitor_id);

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
        return $this->render('public/contact.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'contact_form' => $form->createView(),
            'success_msg' => $success_msg,
            'error_msg' => $error_msg
        ]);
    }
}
