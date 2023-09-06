<?php // command executor (for admin tasks)

    // check if command defined
    if ($site_manager->get_query_string("command") == null) {

        // redirect to 404 page if command is empty
        $site_manager->handle_error("error command is null", 404);
    
    } else {

        // check if user logged in
        if ($user_manager->is_logged_in()) {

            // get command and escapeit
            $command = $site_manager->get_query_string("command");

            // get services path
            $service_dir = $config->get_value('service-dir');

            // get Service name
            $service = str_replace("_Stop", "", $command);
            $service = str_replace("_Start", "", $service);

            // init default final command
            $final_command = "pwd";

            // service starter system
            if (str_ends_with($command, "_Start")) {
                
                // get final start command
                $final_command = $services_list->services[$service]["start_cmd"];
            
                // log to mysql
                $mysql->log("service-manager", "user: ".$user_manager->get_username()." started: $service");
            } 
            
            // service stop system
            elseif (str_ends_with($command, "_Stop")) {

                // get final stop command
                $final_command = $services_list->services[$service]["stop_cmd"];

                // log to mysql
                $mysql->log("service-manager", "user: ".$user_manager->get_username()." stoped: $service");
            }

            // emergency shutdown
            elseif ($command == "shutdown") {

                // log to mysql
                $mysql->log("emergency-shutdown", "user: ".$user_manager->get_username()." used emergency server shutdown");
            
                // execute final command
                $services_manager->execute_command("sudo poweroff");

            // undefind action
            } else {
                $site_manager->redirect_error(403);
            }
        
            // execute final command
            $services_manager->execute_command($final_command);

            // redirect back to dashboard
            $url_utils->js_redirect("?admin=dashboard");

        } else {
            // redirect to 403 page if user not logged in
            $site_manager->handle_error("error this component is only for logged users", 404);

        }
    }
?>