<?php // secret Log disable function (Setup anti log cookie to browser...)

    // check if user logged in 
    if ($userController->isLoggedIn()) {
        
        // check if logging disabled
        if (empty($_COOKIE[$config->getValue('antiLogCookie')])) {

            // set anti log cookie
            $userController->setAntiLogCookie(); 

            // redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        } else {

            // unset anti log cookie
            $cookieUtils->unset_cookie($config->getValue("antiLogCookie"));

            // redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        }    
    } else {

        // check if dev mode enabled
        if ($siteController->isSiteDevMode()) {
            die("[DEV-MODE]:Error: you must log in first");
        } else {
            $siteController->redirectError(403);
        }
    }
?>