<?php

namespace App\Controller\Public;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Image uploader/view controller provides image upload/view component
*/

class ImageUploaderController extends AbstractController
{
    #[Route('/image', name: 'public_image_viewer')]
    public function viewer()
    {
        die('image file');
    }

    #[Route('/image/uploader', name: 'public_image_uploader')]
    public function uploader(): Response
    {
        return $this->render('public/image-uploader.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'linkedin_link' => $_ENV['LINKEDIN_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK']
        ]);
    }
}
