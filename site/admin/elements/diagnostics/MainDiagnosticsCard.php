<div class="cardPhone card text-white bg-dark mb-3" style="margin-left: 1%; margin-right: 1%">
    <div class="card-header">Main diagnostics</div>
    <div class="card-body">
        <?php //Main & basic system checks
        
            //Print main service dir test
            if (!file_exists($pageConfig->getValueByName('serviceDir'))) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>directory: '.$pageConfig->getValueByName('serviceDir').' not exist</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>directory: '.$pageConfig->getValueByName('serviceDir').' was initialized successfully</strong></span></p>';
            }

            //Print ssl test
            if ((!$mainUtils->isSSL() && $siteController->getHTTPhost() != "localhost")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>session is running on http [non secure connction] please contact web admin for fix it</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>page is secured with https</strong></span></p>';
            }

            //Print UFW test
            if (!$servicesController->isServiceInstalled("ufw")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>UFW firewall not installed in system</strong></span></p>';
            } else {
                if (!$servicesController->isUFWRunning()) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>UFW firewall not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>UFW firewall is running</strong></span></p>';
                }
            }

            //Print OpenVPN test
            if (!$servicesController->isServiceInstalled("openvpn")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>OpenVPN not installed in system</strong></span></p>';
            } else {
                if (!$servicesController->ifServiceActive("openvpn")) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>OpenVPN not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>OpenVPN is running</strong></span></p>';
                }
            }

            //Print Apache2 test
            if (!$servicesController->isServiceInstalled("apache2")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Apache2 not installed in system</strong></span></p>';
            } else {
                if (!$servicesController->ifServiceActive("apache2")) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Apache2 not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>Apache2 is running</strong></span></p>';
                }
            }

            //Print MariaDB test
            if (!$servicesController->isServiceInstalled("mariadb")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>MariaDB not installed in system</strong></span></p>';
            } else {
                if (!$servicesController->ifServiceActive("mariadb")) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>MariaDB not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>MariaDB is running</strong></span></p>';
                }
            }

            //Print Tor test
            if (!$servicesController->isServiceInstalled("tor")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Tor not installed in system</strong></span></p>';
            } else {
                if (!$servicesController->ifServiceActive("tor")) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Tor not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>Tor is running</strong></span></p>';
                }
            }

            //Print NextCloud test
            if (!$servicesController->isServiceInstalled("nextcloud")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>NextCloud not installed in system</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>NextCloud installed</strong></span></p>';
            }

            //Print Minecraft server test
            if (!$servicesController->isServiceInstalled("minecraft")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Minecraft server '.$pageConfig->getValueByName('serviceDir').'/minecraft not found, minecraft is not installed</strong></span></p>';
            } else {
                if ($responseUtils->serviceOnlineCheck("127.0.0.1", "25565") == "Offline") {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Mincraft server not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>Mincraft is running</strong></span></p>';
                }            
            }

            //Print team speak server test
            if (!$servicesController->isServiceInstalled("ts3server")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>Teamspeak server '.$pageConfig->getValueByName('serviceDir').'/teamspeak not found, TeamSpeak is not installed</strong></span></p>';
            } else {
                if (!$servicesController->ifProcessRunning("ts3server")) {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>TeamSpeak server not running</strong></span></p>';
                } else {
                    echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>TeamSpeak is running</strong></span></p>';
                }            
            }
        ?>
    </div>
</div>