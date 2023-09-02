<div class="cardPhone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Service status</div>
    <div class="card-body"> 
        <?php // main page of admin with server status
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            echo '<p class="card-text">SERVER: <strong><span class="online-text">Online</span> <span>[<a href="?admin=form&form=shutdown">SHUTDOWN</a>]</span></strong></p>';

            // get services list from /services-list.php file in web root
            $services = $servicesList;

            // execute all services
            foreach ($services as $value) {

                // execute separate service row
                foreach ($value as $value) {

                    // check if service is enabled
                    if ($value["enable"]) {
                        // UFW service define
                        if ($value["service_name"] == "ufw") {

                            // check if UFW running
                            if ($servicesController->isUFWRunning()) {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        } 
                        
                        // teamSpeak server define
                        elseif ($value["service_name"] == "ts3server") {

                            // check if Team speak running
                            if ($servicesController->ifProcessRunning($value["service_name"])) {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }

                        // minecraft server define
                        elseif ($value["service_name"] == "minecraft") {

                            // check if minecraft server starting
                            if (($responseUtils->serviceOnlineCheck("127.0.0.1", "25565") == "Offline") && ($servicesController->checkScreenSession("minecraft"))) {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-info">starting...</span></p>';
                            } else {
                                
                                // check if minecraft server running
                                if ($responseUtils->serviceOnlineCheck("127.0.0.1", "25565") == "Online") {
                                    echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                                } else {
                                    echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                                }
                            }
                        }
                        
                        // others services
                        else {

                            // check if service Online
                            if ($servicesController->ifServiceActive($value["service_name"])) {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-red">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }
                    }
                }
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ?>
    </div>
</div>
