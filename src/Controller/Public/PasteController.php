<?php

namespace App\Controller\Public;

use App\Entity\Paste;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Paste controller provides save/view code paste component
    Page for storing code in database and sharing via url
*/

class PasteController extends AbstractController
{
    private $logManager;
    private $errorManager;
    private $securityUtil;
    private $entityManager;

    public function __construct(
        LogManager $logManager, 
        ErrorManager $errorManager, 
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    #[Route('/paste', name: 'public_code_paste')]
    public function pasteInsert(): Response
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
                $this->errorManager->handleError('error: this paste reached maximum characters 60000', 400);

            } else {

                // save paste to mysql table
                if (!empty($content)) {

                    // init paste entity
                    $paste = new Paste();

                    // set paste data
                    $paste->setName($name);
                    $paste->setContent($content);
                    $paste->setTime($date);

                    // insert new paste
                    try {
                        $this->entityManager->persist($paste);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        $this->errorManager->handleError('error to save new paste, error: '.$e->getMessage(), 500);
                    } 
                } 

                // log new paste
                $this->logManager->log('code-paste', 'saved new paste: '.$name);
            }
        }

        return $this->render('public/paste/paste-save.html.twig');
    }

    #[Route('/paste/view/{token}', name: 'public_code_paste_view')]
    public function pasteView($token): Response
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
            $content = str_replace(array('&lt;', '&gt;'), array('<', '>'), $content);

            $this->logManager->log('code-paste', 'visitor viewed paste: '.$name);
        } else {
            $this->errorManager->handleError('error paste not found', 404);
        }

        return $this->render('public/paste/paste-view.html.twig', ['file' => $token, 'paste_content' => $content]);
    }
}
