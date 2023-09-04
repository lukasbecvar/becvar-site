<?php // secret Log disable function (Setup antilog cookie to browser..)

    // check if user logged in 
    if ($userManager->isLoggedIn()) {
        
        // check if logging disabled
        if (empty($_COOKIE[$config->getValue('antiLogCookie')])) {

            // set anti log cookie
            $userManager->setAntiLogCookie(); 

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
        if ($siteManager->isSiteDevMode()) {
            die("[DEV-MODE]:Error: you must log in first");
        } else {
            $siteManager->redirectError(403);
        }
    }
?>