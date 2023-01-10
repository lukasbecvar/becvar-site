<?php // image viewer component

    // check if image specified
    if (!empty($_GET["spec"])) {

        // get image spec
        $imgSpec = $mysqlUtils->escapeString($_GET["spec"], true, true);

        // get image by specID
        $image = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName("basedb")), "SELECT * FROM image_uploader WHERE imgSpec='".$imgSpec."'"));
    
        // check if image found
        if ($image == NULL) {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        } else {

            // page View
            include($_SERVER['DOCUMENT_ROOT'].'/../site/elements/ImageViewPage.php');
        }

    } else {

        // print error
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: image spec is empty");
        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        }
    }
?>