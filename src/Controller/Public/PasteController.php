<?php

namespace App\Controller\Public;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Paste controller provides paste/view code paste component
*/

class PasteController extends AbstractController
{
    #[Route('/paste', name: 'public_code_paste')]
    public function viewer()
    {
        die('paste');
    }
}
