<?php //Command executor (for admin tasks)

    //Check if command defined
    if (empty($_GET["command"])) {

        //Redirect to 404 page if command is empty
        $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
    
    } else {

        //Check if user logged in
        if ($adminController->isLoggedIn()) {

            //Get command and escapeit
            $command = $mysqlUtils->escapeString($_GET["command"], true, true); 

            //Init services path
            $serviceDir = $pageConfig->getValueByName('serviceDir');

            //Get Service name
            $service = str_replace("_Stop", "", $command);
            $service = str_replace("_Start", "", $service);

            //Init default final command
            $finalCommand = "pwd";

            //Service starter system
            if (str_ends_with($command, "_Start")) {
                
                //Get final start command
                $finalCommand = $servicesList->services[$service]["start_cmd"];
            
                //Log to mysql
                $mysqlUtils->logToMysql("Service", "$service start");
            } 
            
            //Service stop system
            elseif (str_ends_with($command, "_Stop")) {

                //Get final stop command
                $finalCommand = $servicesList->services[$service]["stop_cmd"];

                //Log to mysql
                $mysqlUtils->logToMysql("Service", "$service stop");

            //Undefind action
            } else {
                $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
            }
        
            //Execute final command
            $servicesController->executeCommand($finalCommand);

            //Redirect back to dashboard
            $urlUtils->jsRedirect("?admin=dashboard");

        } else {
            //Redirect to 403 page if user not logged in
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
        }
    }
?>