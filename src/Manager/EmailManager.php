<?php

namespace App\Manager;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class EmailManager
 *
 * Email manager is a service class for sending emails
 *
 * @package App\Manager
 */
class EmailManager
{
    private LogManager $logManager;
    private MailerInterface $mailer;
    private ErrorManager $errorManager;

    public function __construct(LogManager $logManager, MailerInterface $mailer, ErrorManager $errorManager)
    {
        $this->mailer = $mailer;
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
    }

    /**
     * Sends a default email with a subject and message to a recipient.
     *
     * @param string $recipient The email address of the recipient.
     * @param string $subject The subject of the email.
     * @param string $message The message of the email.
     *
     * @return void
     */
    public function sendDefaultEmail(string $recipient, string $subject, string $message): void
    {
        $this->sendEmail($recipient, $subject, [
            'subject' => $subject,
            'message' => $message
        ]);
    }

    /**
     * Sends an email with a subject and context to a recipient.
     *
     * @param string $recipient The email address of the recipient.
     * @param string $subject The subject of the email.
     * @param array<mixed> $context The context of the email.
     * @param string $template The template of the email.
     *
     * @return void
     */
    public function sendEmail(string $recipient, string $subject, array $context, string $template = 'default'): void
    {
        // check if mailer is enabled
        if ($_ENV['MAILER_ENABLED'] == 'false') {
            return;
        }

        // build email message
        $email = (new TemplatedEmail())
            ->from($_ENV['MAILER_USERNAME'])
            ->to($recipient)
            ->subject($subject)
            ->htmlTemplate('email/' . $template . '.html.twig')
            ->context($context);

        try {
            // send email
            $this->mailer->send($email);

            // log email sending
            $this->logManager->log('email-send', 'Email sent to ' . $recipient . ' with subject: ' . $subject);
        } catch (TransportExceptionInterface $e) {
            $this->errorManager->handleError('Email sending failed: ' . $e->getMessage(), 500);
        }
    }
}
