<?php

namespace App\Controller\Admin;

use App\Util\SiteUtil;
use App\Form\EmailSendType;
use App\Manager\AuthManager;
use App\Manager\EmailManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class EmailSendServiceController
 *
 * Controller for handling email sending in the admin panel.
 *
 * @package App\Controller\Admin
 */
class EmailSendServiceController extends AbstractController
{
    private SiteUtil $siteUtil;
    private AuthManager $authManager;
    private EmailManager $emailManager;

    public function __construct(SiteUtil $siteUtil, AuthManager $authManager, EmailManager $emailManager)
    {
        $this->siteUtil = $siteUtil;
        $this->authManager = $authManager;
        $this->emailManager = $emailManager;
    }

    /**
     * Handles email sending via the admin panel.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    #[Route('/admin/email/send', methods: ['GET', 'POST'], name: 'admin_email_send')]
    public function sendEmail(Request $request): Response
    {
        $status = $this->siteUtil->getQueryString('status', $request);

        // create form
        $form = $this->createForm(EmailSendType::class);

        // handle request
        $form->handleRequest($request);

        // check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // get form data
            $data = $form->getData();

            // send email
            $this->emailManager->sendEmail($data['email'], $data['subject'], $data['message']);

            // redirect to email send page
            return $this->redirectToRoute('admin_email_send', ['status' => 'success']);
        }

        // render email send page
        return $this->render('admin/email-send-service.html.twig', [
            // user data
            'user_name' => $this->authManager->getUsername(),
            'user_role' => $this->authManager->getUserRole(),
            'user_pic' => $this->authManager->getUserProfilePic(),

            // email manager form
            'status' => $status,
            'form' => $form->createView(),

            'mailer_state' => $_ENV['MAILER_ENABLED']
        ]);
    }
}
