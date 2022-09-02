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

            //Tor service
            if ($command == "torStop") {
                $servicesController->executeScriptAsROOT("services/tor_stop.sh");

            } elseif ($command == "torStart") {
                $servicesController->executeScriptAsROOT("services/tor_start.sh");
            ////////////////////////////////////////////////////////////////////////////

            //OpenVPN service
            } elseif ($command == "openvpnStop") {
                $servicesController->executeScriptAsROOT("services/openvpn_stop.sh");

            } elseif ($command == "openvpnStart") {
                $servicesController->executeScriptAsROOT("services/openvpn_start.sh");
            ////////////////////////////////////////////////////////////////////////////

            //UFW firewall
            } elseif ($command == "ufwStop") {
                $servicesController->executeScriptAsROOT("services/ufw_disable.sh");

            } elseif ($command == "ufwStart") {
                $servicesController->executeScriptAsROOT("services/ufw_enable.sh");
            ////////////////////////////////////////////////////////////////////////////

            //Apache service
            } elseif ($command == "apacheStop") {
                $servicesController->executeScriptAsROOT("services/apache_stop.sh");
            ////////////////////////////////////////////////////////////////////////////

            //SSHD service 
            } elseif ($command == "sshdStart") {
                $servicesController->executeScriptAsROOT("services/sshd_start.sh");
            
            } elseif ($command == "sshdStop") {
                $servicesController->executeScriptAsROOT("services/sshd_stop.sh");
            ////////////////////////////////////////////////////////////////////////////

            //TeamSpeak service
            } elseif ($command == "ts3serverStop") {
                $servicesController->executeScriptAsROOT("services/teamspeak_stop.sh");

            } elseif ($command == "ts3serverStart") {
                $servicesController->executeScriptAsROOT("services/teamspeak_start.sh");
            ////////////////////////////////////////////////////////////////////////////

            //Mariadb service
            } elseif ($command == "mariadbStop") {
                $servicesController->executeScriptAsROOT("services/mariadb_stop.sh");
            ////////////////////////////////////////////////////////////////////////////

            //Minecraft server
            } elseif ($command == "minecraftStop") {
                $servicesController->executeScriptAsROOT("services/minecraft_stop.sh");

            } elseif ($command == "minecraftStart") {
                $servicesController->executeScriptAsROOT("services/minecraft_start.sh");
            ////////////////////////////////////////////////////////////////////////////

            //Dubinek
            } elseif ($command == "dubinekStop") {
                $servicesController->executeScriptAsROOT("services/dubinek_stop.sh");

            } elseif ($command == "dubinekStart") {
                $servicesController->executeScriptAsROOT("services/dubinek_start.sh");
            ////////////////////////////////////////////////////////////////////////////

            //If command not found
            } else {
                $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
            }

            //Redirect back to dashboard
            $urlUtils->jsRedirect("?admin=dashboard");

        } else {
            //Redirect to 403 page if user not logged in
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
        }
    }
?>