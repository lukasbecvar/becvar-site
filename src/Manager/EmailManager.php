<?php

namespace App\Manager;

use Twig\Environment;
use App\Util\VisitorInfoUtil;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Class EmailManager
 *
 * Email manager is a service class for sending emails
 *
 * @package App\Manager
 */
class EmailManager
{
    private Environment $twig;
    private MailerInterface $mailer;
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(MailerInterface $mailer, Environment $twig, ErrorManager $errorManager, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->errorManager = $errorManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Send email to recipients with title, subject and message
     *
     * @param array<string> $recipients The list of recipients
     * @param string $title The title of the email
     * @param string $subject The subject of the email
     * @param string $message The message of the email
     * @param string $from The sender of the email
     *
     * @return void
     */
    public function sendEmail(array $recipients, string $title, string $subject, string $message, string $from): void
    {
        // check if mailer is enabled
        if ($_ENV['MAILER_ENABLED'] == 'false') {
            return;
        }

        // render email template
        $body = $this->twig->render('email/default.html.twig', [
            'title' => $title,
            'subject' => $subject,
            'time' => date('d.m.Y H:i:s'),
            'message' => $message,
            'ip_address' => $this->visitorInfoUtil->getIP(),
        ]);

        // create email object
        $email = (new Email())
            ->from($from)
            ->to(...$recipients)
            ->subject($subject)
            ->html($body);

        // send email
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->errorManager->handleError('Email sending failed: ' . $e->getMessage(), 500);
        }
    }
}
