<?php

namespace App\Service\Manager;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Class EmailManager
 *
 * Service class for managing email sending operations.
 * 
 * @package App\Service\Manager
 */
class EmailManager 
{
    /**
     * @var MailerInterface 
     * The mailer instance used for sending emails.
     */
    private MailerInterface $mailer;

    /**
     * @var ErrorManager 
     * The error manager instance used for handling errors.
     */
    private ErrorManager $errorManager;

    /**
     * EmailManager constructor.
     *
     * @param MailerInterface $mailer 
     * @param ErrorManager    $errorManager
     */
    public function __construct(MailerInterface $mailer, ErrorManager $errorManager)
    {
        $this->mailer = $mailer;
        $this->errorManager = $errorManager;
    }

    /**
     * Sends an email.
     *
     * @param string $email The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $message The content of the email.
     * @throws \Exception If an error occurs while sending the email.
     */
    public function sendEmail(string $email, string $subject, string $message): void 
    {
        // check if email sending is enabled
        if ($_ENV['MAILER_ENABLED'] == 'true') {
            try {
                // build a templated email 
                $email = (new TemplatedEmail())
                    ->from('lukas@becvar.xyz')
                    ->to($email)
                    ->subject($subject)
                    ->htmlTemplate('common/email/email-message.html.twig')
                    ->context([
                        'subject' => $subject,
                        'message' => $message
                    ]);
    
                // send the email
                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to process email: '.$e->getMessage(), 500);
            }
        }
    }
}
