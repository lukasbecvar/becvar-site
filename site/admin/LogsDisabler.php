<?php //Secret Log disable function (Setup anti log cookie to browser...)

    //Check if user logged in 
    if ($adminController->isLoggedIn()) {
        
        //Check if logging disabled
        if (empty($_COOKIE[$pageConfig->getValueByName('antiLogCookie')])) {

            //Set anti log cookie
            $adminController->setAntiLogCookie(); 

            //Redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        } else {

            //Unset anti log cookie
            $cookieUtils->unset_cookie($pageConfig->getValueByName("antiLogCookie"));

            //Redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        }    
    } else {
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: you must log in first");
        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
        }
    }
?>