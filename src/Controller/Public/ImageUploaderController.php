<?php

namespace App\Controller\Public;

use App\Entity\Image;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Image uploader/view controller provides image upload/view component
    Page for storing images in database and sharing via url
*/

class ImageUploaderController extends AbstractController
{
    private $logManager;
    private $errorManager;
    private $secutityUtil;
    private $entityManager;

    public function __construct(
        LogManager $logManager,
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->secutityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    #[Route('/image/view/{token}', name: 'public_image_viewer')]
    public function imageView($token): Response
    {
        // escape image token
        $token = $this->secutityUtil->escapeString($token);

        // default image
        $image_content = null;

        // get image data
        $imageRepo = $this->entityManager->getRepository(Image::class)->findOneBy(['token' => $token]);
        
        // check if image found
        if ($imageRepo !== null) {
            $image_content = $imageRepo->getImage();
        }

        // check if image found
        if ($image_content == null) {
            $this->errorManager->handleError('not found error, image: '.$token.', not found in database', 404);
        } else {
            $this->logManager->log('image-uploader', 'visitor viewed paste: '.$token);
            return $this->render('public/image/image-viewer.html.twig', [
                'token' => $token,
                'image' => $image_content
            ]);
        }
    }

    #[Route('/image/uploader', name: 'public_image_uploader')]
    public function uploadImage(): Response
    {
        // default error msg
        $error_msg = null;

        // check if form is submited
        if (isset($_POST['submitUpload'])) { 
        
            // extract file extension
            $ext = substr(strrchr($_FILES['userfile']['name'], '.'), 1);      
            
            // check if file is image
            if ($ext == 'gif' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'png') {		
                
                // generate img_spec value
                $token = ByteString::fromRandom(32)->toByteString();
                
                // get image file
                $image_file = file_get_contents($_FILES['userfile']['tmp_name']);
    
                // encode file
                $image_file = base64_encode($image_file);

                // escape image string
                $image_file = $this->secutityUtil->escapeString($image_file);
    
                // get current data
                $date = date('d.m.Y H:i:s');
    
                // init image entity
                $image = new Image();

                // set image data
                $image->setToken($token);
                $image->setImage($image_file);
                $image->setTime($date);

                // try to upload image
                try {
                    $this->entityManager->persist($image);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $this->errorManager->handleError('error to upload image: '.$token.', '.$e->getMessage(), 400);
                }

                // log to database
                $this->logManager->log('image-uploader', 'uploaded new image: '.$token);	

                // redirect to image view
                return $this->redirectToRoute('public_image_viewer', ['token' => $token]);

            } else {
                // handle error
                $error_msg = 'Error file have wrong format!';
            }
        }

        return $this->render('public/image/image-uploader.html.twig', [
            'instagram_link' => $_ENV['INSTAGRAM_LINK'],
            'linkedin_link' => $_ENV['LINKEDIN_LINK'],
            'telegram_link' => $_ENV['TELEGRAM_LINK'],
            'contact_email' => $_ENV['CONTACT_EMAIL'],
            'twitter_link' => $_ENV['TWITTER_LINK'],
            'github_link' => $_ENV['GITHUB_LINK'],
            'error_msg' => $error_msg
        ]);
    }
}
