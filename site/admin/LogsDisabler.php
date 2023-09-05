<?php // secret Log disable function (Setup antilog cookie to browser..)

    // check if user logged in 
    if ($userManager->isLoggedIn()) {
        
        // check if logging disabled
        if (empty($_COOKIE[$config->getValue('anti-log-cookie')])) {

            // set anti log cookie
            $userManager->setAntiLogCookie(); 

            // redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        } else {

            // unset anti log cookie
            $cookieUtils->unset_cookie($config->getValue("anti-log-cookie"));

            // redirect back to admin
            $urlUtils->jsRedirect("?admin=dashboard");
        }    
    } else {

        // handle error
        $siteManager->handleError("[DEV-MODE]:Error: you must login first", 403);
    }
?>