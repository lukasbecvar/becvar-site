<?php // command executor (for admin tasks)

    // check if command defined
    if ($siteManager->getQueryString("command") == null) {

        // redirect to 404 page if command is empty
        $siteManager->redirectError(404);
    
    } else {

        // check if user logged in
        if ($userManager->isLoggedIn()) {

            // get command and escapeit
            $command = $siteManager->getQueryString("command");

            // get services path
            $serviceDir = $config->getValue('serviceDir');

            // get Service name
            $service = str_replace("_Stop", "", $command);
            $service = str_replace("_Start", "", $service);

            // init default final command
            $finalCommand = "pwd";

            // service starter system
            if (str_ends_with($command, "_Start")) {
                
                // get final start command
                $finalCommand = $servicesList->services[$service]["start_cmd"];
            
                // log to mysql
                $mysql->logToMysql("service-manager", "user: ".$userManager->getCurrentUsername()." started: $service");
            } 
            
            // service stop system
            elseif (str_ends_with($command, "_Stop")) {

                // get final stop command
                $finalCommand = $servicesList->services[$service]["stop_cmd"];

                // log to mysql
                $mysql->logToMysql("service-manager", "user: ".$userManager->getCurrentUsername()." stoped: $service");
            }

            // emergency shutdown
            elseif ($command == "shutdown") {

                // log to mysql
                $mysql->logToMysql("emergency-shutdown", "user: ".$userManager->getCurrentUsername()." used emergency server shutdown");
            
                // execute final command
                $servicesManager->executeCommand("sudo poweroff");

            // undefind action
            } else {
                $siteManager->redirectError(403);
            }
        
            // execute final command
            $servicesManager->executeCommand($finalCommand);

            // redirect back to dashboard
            $urlUtils->jsRedirect("?admin=dashboard");

        } else {
            // redirect to 403 page if user not logged in
            $siteManager->redirectError(403);
        }
    }
?>