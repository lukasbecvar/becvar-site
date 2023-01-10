<?php // command executor (for admin tasks)

    // check if command defined
    if (empty($_GET["command"])) {

        // redirect to 404 page if command is empty
        $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
    
    } else {

        // check if user logged in
        if ($adminController->isLoggedIn()) {

            // get command and escapeit
            $command = $mysqlUtils->escapeString($_GET["command"], true, true); 

            // get services path
            $serviceDir = $pageConfig->getValueByName('serviceDir');

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
                $mysqlUtils->logToMysql("Service", "$service start");
            } 
            
            // service stop system
            elseif (str_ends_with($command, "_Stop")) {

                // get final stop command
                $finalCommand = $servicesList->services[$service]["stop_cmd"];

                // log to mysql
                $mysqlUtils->logToMysql("Service", "$service stop");

            // undefind action
            } else {
                $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
            }
        
            // execute final command
            $servicesController->executeCommand($finalCommand);

            // redirect back to dashboard
            $urlUtils->jsRedirect("?admin=dashboard");

        } else {
            // redirect to 403 page if user not logged in
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
        }
    }
?>