<?php // image viewer component

    // check if image specified
    if ($siteController->getQueryString("spec") != null) {

        // get image spec
        $imgSpec = $siteController->getQueryString("spec");

        // get image by specID
        $image = $mysqlUtils->fetchValue("SELECT image FROM image_uploader WHERE imgSpec='".$imgSpec."'", "image");
    
        // check if image found
        if ($image == NULL) {
            $siteController->redirectError(404);
        } else {

            // page View
            include($_SERVER['DOCUMENT_ROOT'].'/../site/elements/ImageViewPage.php');
        }

    } else {

        // print error
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: image spec is empty");
        } else {
            $siteController->redirectError(404);
        }
    }
?>