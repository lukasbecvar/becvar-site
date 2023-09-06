<?php // secret Log disable function (Setup antilog cookie to browser..)

    // check if user logged in 
    if ($user_manager->is_logged_in()) {
        
        // check if logging disabled
        if (empty($_COOKIE[$config->get_value('anti-log-cookie')])) {

            // set anti log cookie
            $user_manager->set_anti_log_cookie(); 

            // redirect back to admin
            $url_utils->js_redirect("?admin=dashboard");
        } else {

            // unset anti log cookie
            $cookie_utils->unset($config->get_value("anti-log-cookie"));

            // redirect back to admin
            $url_utils->js_redirect("?admin=dashboard");
        }    
    } else {

        // handle error
        $site_manager->handle_error("[DEV-MODE]:Error: you must login first", 403);
    }
?>