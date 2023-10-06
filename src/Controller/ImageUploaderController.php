<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageUploaderController extends AbstractController
{
    #[Route('/image', name: 'app_image_viewer')]
    public function viewer()
    {
        die('image file');
    }

    #[Route('/image/uploader', name: 'app_image_uploader')]
    public function uploader(): Response
    {
        return $this->render('public/image-uploader.html.twig');
    }
}
