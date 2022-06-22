<?php //Command executor (for admin tasks)

    //Check if command defined
    if (empty($_GET["command"])) {

        $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
    
    } else {

        //Check if user logged in
        if ($adminController->isLoggedIn()) {

            //Get command and escapeit
            $command = $mysqlUtils->escapeString($_GET["command"], true, true); 

            //Init services path
            $serviceDir = $pageConfig->getValueByName('serviceDir');


            //Tor service
            if ($command == "torStop") {
                $servicesController->executeScriptAsROOT("tor_stop.sh");

            } elseif ($command == "torStart") {
                $servicesController->executeScriptAsROOT("tor_start.sh");




            //OpenVPN service
            } elseif ($command == "openvpnStop") {
                $servicesController->executeScriptAsROOT("openvpn_stop.sh");

            } elseif ($command == "openvpnStart") {
                $servicesController->executeScriptAsROOT("openvpn_start.sh");




            //UFW firewall
            } elseif ($command == "ufwStop") {
                $servicesController->executeScriptAsROOT("ufw_disable.sh");

            } elseif ($command == "ufwStart") {
                $servicesController->executeScriptAsROOT("ufw_enable.sh");




            //Apache service
            } elseif ($command == "apacheStop") {
                $servicesController->executeScriptAsROOT("apache_stop.sh");

            } elseif ($command == "apacheStart") {
                $servicesController->executeScriptAsROOT("apache_start.sh");




            //TeamSpeak service
            } elseif ($command == "ts3serverStop") {
                $servicesController->executeScriptAsROOT("teamspeak_stop.sh");

            } elseif ($command == "ts3serverStart") {
                $servicesController->executeScriptAsROOT("teamspeak_start.sh");




            //Mariadb service
            } elseif ($command == "mariadbStop") {
                $servicesController->executeScriptAsROOT("mariadb_stop.sh");

            } elseif ($command == "mariadbStart") {
                $servicesController->executeScriptAsROOT("mariadb_start.sh");




            //Minecraft server
            } elseif ($command == "minecraftStop") {
                $servicesController->executeScriptAsROOT("minecraft_stop.sh");

            } elseif ($command == "minecraftStart") {
                $servicesController->executeScriptAsROOT("minecraft_start.sh");




            //Dubinek
            } elseif ($command == "dubinekStop") {
                $servicesController->executeScriptAsROOT("dubinek_stop.sh");

            } elseif ($command == "dubinekStart") {
                $servicesController->executeScriptAsROOT("dubinek_start.sh");



            //If command not found
            } else {
                $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
            }

            //Redirect back to dashboard
            $urlUtils->jsRedirect("index.php?page=admin&process=dashboard");

        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
        }
    }
?>
