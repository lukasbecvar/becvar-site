<?php

namespace App\Controller\Public;

use App\Entity\Message;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Form\ContactFormType;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Contact controller provides contact links & contact form
*/

class ContactController extends AbstractController
{   
    private $logHelper;
    private $securityUtil;
    private $entityManager;
    private $visitorInfoUtil;

    public function __construct(
        LogHelper $logHelper, 
        SecurityUtil $securityUtil, 
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    #[Route('/contact', name: 'public_contact')]
    public function index(Request $request): Response
    {
        // default msg state
        $error_msg = null;
        $success_msg = null;
        $found_error = false;

        // handle success status
        if (!empty($request->query->get('status')) && $request->query->get('status') == 'ok') {
            $success_msg = 'Your message has been sent, Lukáš replies within 24 hours at most';
        }

        // handle error status
        if (!empty($request->query->get('status')) && $request->query->get('status') == 'ko') {
            $error_msg = 'Your message was not sent due to an error, please try again later, or check your message inputs';
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
                $error_msg = 'Please enter your username';
                $found_error = true;
            } else if (empty($email)) {
                $error_msg = 'Please enter your email';
                $found_error = true;
            } else if (empty($message_input)) {
                $error_msg = 'Please enter your message';
                $found_error = true;
            } else if (strlen($message_input) > 200) {
                $error_msg = 'Maximal message lenght is 200 characters';
                $found_error = true;

            // check if honeypot is empty
            } elseif (isset($honeypot)) {
                $error_msg = 'Your message has been blocked';
                $found_error = true;
                $this->logHelper->log('message-sender', 'message: '.$message_input.', has been blocked: honeypot used');
            } else {

                // escape values (XSS protection)
                $name = $this->securityUtil->escapeString($name);
                $email = $this->securityUtil->escapeString($email);
                $message_input = $this->securityUtil->escapeString($message_input);

                // check if error not found
                if ($found_error == false) {

                    // get others data
                    $date = date('d.m.Y H:i:s');
                    $ip_address = $this->visitorInfoUtil->getIP();

                    // update visitor email
                    $this->visitorInfoUtil->updateVisitorEmail($ip_address, $email);

                    // set message entity values
                    $message->setName($name);
                    $message->setEmail($email);
                    $message->setMessage($message_input);
                    $message->setTime($date);
                    $message->setIpAddress($ip_address);
                    $message->setStatus('open');

                    // insert new message
                    try {
                        $this->entityManager->persist($message);
                        $this->entityManager->flush();
                        
                        // redirect back to from & handle success status
                        return $this->redirectToRoute('public_contact', ['status' => 'ok', 'final' => 'yes']);
                    } catch (\Exception) {
                        // redirect back to from & handle error status
                        return $this->redirectToRoute('public_contact', ['status' => 'ko', 'final' => 'yes']);
                    }
                }
            }
        }

        // render contact page
        return $this->render('public/contact.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'linkedin_link' => $_ENV['LINKEDIN_LINK'],
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