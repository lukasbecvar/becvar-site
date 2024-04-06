<?php

namespace App\Controller\Public;

use App\Entity\Paste;
use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Service\Manager\LogManager;
use App\Service\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PasteController
 * 
 * Paste controller provides save/view code paste component
 * Page for storing code in the database and sharing via URL.
 * 
 * @package App\Controller\Public
 */
class PasteController extends AbstractController
{
    private SiteUtil $siteUtil;
    private LogManager $logManager;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SiteUtil $siteUtil,
        LogManager $logManager, 
        ErrorManager $errorManager, 
        SecurityUtil $securityUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
    }

    /**
     * Insertion of code pastes.
     *
     * @return Response Returns a Response object representing the HTTP response.
     *
     * @throws \Exception Throws an exception if there is an error during the code paste insertion process.
     */
    #[Route('/paste', methods: ['GET', 'POST'], name: 'public_code_paste')]
    public function pasteInsert(): Response
    {
        // check if paste submited
        if (isset($_POST['data'])) {
        
            // get data from post request
            $content = $_POST['data'];
            $name = $this->securityUtil->escapeString($_POST['file']);

            // get upload date
            $date = date('d.m.Y H:i:s');
                
            // check if maximum lenght reached
            if (strlen($content) > 60001) {

                // redirect error
                return $this->errorManager->handleError('error: this paste reached maximum characters 60000', 400);
            } else {
                // save paste data
                if (!empty($content)) {

                    // init paste entity
                    $paste = new Paste();

                    // encrypt paste content
                    $content = $this->securityUtil->encryptAes($content);

                    // set paste data
                    $paste->setName($name);
                    $paste->setContent($content);
                    $paste->setTime($date);

                    // insert new paste
                    try {
                        $this->entityManager->persist($paste);
                        $this->entityManager->flush();
                    } catch (\Exception $e) {
                        return $this->errorManager->handleError('error to save new paste, error: '.$e->getMessage(), 500);
                    } 
                    
                    // log new paste
                    $this->logManager->log('code-paste', 'saved new paste: '.$name);
                } 
            }
        }

        return $this->render('public/paste/paste-save.html.twig');
    }

    /**
     * Displays the page for viewing a code paste.
     *
     * @param Request $request The HTTP request.
     * @return Response The response containing the rendered paste view page.
     */
    #[Route('/paste/view', methods: ['GET'], name: 'public_code_paste_view')]
    public function pasteView(Request $request): Response
    {
        $content = null;
     
        // get paste token
        $token = $this->siteUtil->getQueryString('token', $request);

        // get paste spec name
        $name = $this->securityUtil->escapeString($token);

        // get paste data
        $pasteContent = $this->entityManager->getRepository(Paste::class)->findOneBy(['name' => $name]);
        
        // check if paste found
        if ($pasteContent !== null) {

            // get content & decrypt
            $content = $this->securityUtil->decryptAes($pasteContent->getContent());

            // check if paste content is decrypted
            if ($content == null) {
                $this->errorManager->handleError('Error to decrypt aes paste content', 500);
            }

            // replace xss (Escape [XSS Protection])
            $content = str_replace(array('&lt;', '&gt;'), array('<', '>'), $content);

            $this->logManager->log('code-paste', 'visitor viewed paste: '.$name);
        } else {
            return $this->errorManager->handleError('error paste not found', 404);
        }

        // return code paste view
        return $this->render('public/paste/paste-view.html.twig', ['file' => $token, 'paste_content' => $content]);
    }
}
