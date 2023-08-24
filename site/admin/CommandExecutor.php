<?php // command executor (for admin tasks)

    // check if command defined
    if ($siteController->getQueryString("command") == null) {

        // redirect to 404 page if command is empty
        $siteController->redirectError(404);
    
    } else {

        // check if user logged in
        if ($adminController->isLoggedIn()) {

            // get command and escapeit
            $command = $siteController->getQueryString("command");

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
                $mysql->logToMysql("Service", "$service start");
            } 
            
            // service stop system
            elseif (str_ends_with($command, "_Stop")) {

                // get final stop command
                $finalCommand = $servicesList->services[$service]["stop_cmd"];

                // log to mysql
                $mysql->logToMysql("Service", "$service stop");

            // undefind action
            } else {
                $siteController->redirectError(403);
            }
        
            // execute final command
            $servicesController->executeCommand($finalCommand);

            // redirect back to dashboard
            $urlUtils->jsRedirect("?admin=dashboard");

        } else {
            // redirect to 403 page if user not logged in
            $siteController->redirectError(403);
        }
    }
?>