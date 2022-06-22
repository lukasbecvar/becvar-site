<?php //Image viewer component

    //Check if image specified
    if (!empty($_GET["spec"])) {

        //Get image spec
        $imgSpec = $mysqlUtils->escapeString($_GET["spec"], true, true);

        //Get image by specID
        $image = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName("basedb")), "SELECT * FROM image_uploader WHERE imgSpec='".$imgSpec."'"));
    
        if ($image == NULL) {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        } else {

            //Include page View
            include_once("elements/public/ImageViewPage.php");
        }

    } else {
        if ($pageConfig->getValueByName("dev_mode") == true) {
            die("[DEV-MODE]:Error: image spec is empty");
        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        }
    }
?>