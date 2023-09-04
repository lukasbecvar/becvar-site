<?php // image viewer component

    // check if image specified
    if ($siteManager->getQueryString("spec") != null) {

        // get image spec
        $imgSpec = $siteManager->getQueryString("spec");

        // get image by specID
        $image = $mysql->fetchValue("SELECT image FROM image_uploader WHERE imgSpec='".$imgSpec."'", "image");
    
        // check if image found
        if ($image == null) {
            $siteManager->redirectError(404);
        } else {

            // page View
            include($_SERVER['DOCUMENT_ROOT'].'/../site/elements/ImageViewPage.php');
        }

    } else {

        // print error
        if ($siteManager->isSiteDevMode()) {
            die("[DEV-MODE]:Error: image spec is empty");
        } else {
            $siteManager->redirectError(404);
        }
    }
?>