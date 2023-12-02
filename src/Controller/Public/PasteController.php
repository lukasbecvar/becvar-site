<?php

namespace App\Controller\Public;

use App\Entity\Paste;
use App\Util\SiteUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Paste controller provides save/view code paste component
 * Page for storing code in the database and sharing via URL.
 */
class PasteController extends AbstractController
{
    /** * @var SiteUtil */
    private SiteUtil $siteUtil;

    /** * @var LogManager */
    private LogManager $logManager;

    /** * @var ErrorManager */
    private ErrorManager $errorManager;

    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /** * @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * PasteController constructor.
     *
     * @param SiteUtil               $siteUtil
     * @param LogManager             $logManager
     * @param ErrorManager           $errorManager
     * @param SecurityUtil           $securityUtil
     * @param EntityManagerInterface $entityManager
     */
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
     * Displays the page for saving a code paste.
     *
     * @return Response The response containing the rendered paste save page.
     */
    #[Route('/paste', methods: ['GET', 'POST'], name: 'public_code_paste')]
    public function pasteInsert(): Response
    {
        // check if paste submited
        if (isset($_POST['data'])) {
        
            // get data from post & escape
            $content = $_POST['data'];
            $name = $this->securityUtil->escapeString($_POST['file']);

            // get upload date
            $date = date('d.m.Y H:i:s');
                
            // check if maximum lenght reached
            if (strlen($content) > 60001) {

                // redirect error
                return $this->errorManager->handleError('error: this paste reached maximum characters 60000', 400);

            } else {

                // save paste to mysql table
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
                } 

                // log new paste
                $this->logManager->log('code-paste', 'saved new paste: '.$name);
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

            // replace xss (Escape [XSS Protection])
            $content = str_replace(array('&lt;', '&gt;'), array('<', '>'), $content);

            $this->logManager->log('code-paste', 'visitor viewed paste: '.$name);
        } else {
            return $this->errorManager->handleError('error paste not found', 404);
        }

        return $this->render('public/paste/paste-view.html.twig', ['file' => $token, 'paste_content' => $content]);
    }
}
