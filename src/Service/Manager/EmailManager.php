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
     * @var LogManager
     * Instance of the LogManager for handling log-related functionality.
     */
    private LogManager $logManager;

    /**
     * EmailManager constructor.
     *
     * @param MailerInterface $mailer 
     * @param LogManager    $logManager
     */
    public function __construct(MailerInterface $mailer, LogManager $logManager)
    {
        $this->mailer = $mailer;
        $this->logManager = $logManager;
    }

    /**
     * Sends an email.
     *
     * @param string $email_to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $message The content of the email.
     * @throws \Exception If an error occurs while sending the email.
     */
    public function sendEmail(string $email_to, string $subject, string $message): void 
    {
        // check if email sending is enabled
        if ($_ENV['MAILER_ENABLED'] == 'true') {
            try {
                // build a templated email 
                $email = (new TemplatedEmail())
                    ->from($_ENV['MAILER_USERNAME'])
                    ->to($email_to)
                    ->subject($subject)
                    ->htmlTemplate('common/email/email-message.html.twig')
                    ->context([
                        'subject' => $subject,
                        'message' => $message
                    ]);
    
                // send the email
                $this->mailer->send($email);
            } catch (\Exception $e) {
                $this->logManager->log('internal-error', 'error to send email: '.$subject.' to: '.$email_to.', error: '.$e->getMessage(), true);
            }
        }
    }
}
