<?php //Image viewer component

    //Check if image specified
    if (!empty($_GET["spec"])) {

        //Get image spec
        $imgSpec = $mysqlUtils->escapeString($_GET["spec"], true, true);

        //Get image by specID
        $image = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName("basedb")), "SELECT * FROM image_uploader WHERE imgSpec='".$imgSpec."'"));
    
        //Check if image found
        if ($image == NULL) {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        } else {

            //Include page View
            include($_SERVER['DOCUMENT_ROOT'].'/../site/elements/ImageViewPage.php');
        }

    } else {
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: image spec is empty");
        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        }
    }
?>