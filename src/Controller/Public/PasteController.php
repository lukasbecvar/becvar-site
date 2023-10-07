<?php

namespace App\Controller\Public;

use App\Entity\Paste;
use App\Helper\LogHelper;
use App\Util\SecurityUtil;
use App\Helper\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Paste controller provides save/view code paste component
*/

class PasteController extends AbstractController
{
    private $logHelper;
    private $errorHelper;
    private $securityUtil;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper, 
        ErrorHelper $errorHelper, 
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->errorHelper = $errorHelper;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    #[Route('/paste', name: 'public_code_paste')]
    public function new(): Response
    {
        // check if paste submited
        if (isset($_POST['data'])) {
        
            // get data from post & escape
            $content = $this->securityUtil->escapeString($_POST['data']);
            $name = $this->securityUtil->escapeString($_POST['file']);

            // get upload date
            $date = date('d.m.Y H:i:s');
                
            // check if maximum lenght reached
            if (strlen($content) > 60001) {

                // redirect error
                $this->errorHelper->handleError('error: this paste reached maximum characters 60000', 400);

            } else {

                // save paste to mysql table
                if (!empty($content)) {

                    // init paste entity
                    $paste = new Paste();

                    // set paste data
                    $paste->setName($name);
                    $paste->setContent($content);
                    $paste->setDate($date);

                    // insert new paste
                    try {
                        $this->entityManager->persist($paste);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->errorHelper->handleError('error to save new paste, error: '.$e->getMessage(), 500);
                    } 
                } 

                // log new paste
                $this->logHelper->log("paste", "saved new paste: ".$name);
            }
        }

        return $this->render('public/paste/paste-save.html.twig');
    }

    #[Route('/paste/view/{token}', name: 'public_code_paste_view')]
    public function viewer($token): Response
    {
        $content = null;
     
        // get paste spec name
        $name = $this->securityUtil->escapeString($token);

        // get paste data
        $pasteContent = $this->entityManager->getRepository(Paste::class)->findOneBy(['name' => $name]);
        
        // check if paste found
        if ($pasteContent !== null) {
            $content = $pasteContent->getContent();

            // replace xss (Escape [XSS Protection])
            $content = str_replace(array("&lt;", "&gt;"), array("<", ">"), $content);
        } else {
            $this->errorHelper->handleError('error paste not found', 404);
        }

        return $this->render('public/paste/paste-view.html.twig', ['file' => $token, 'paste_content' => $content]);
    }
}
