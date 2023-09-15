<div class="card-phone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Service status</div>
    <div class="card-body"> 
        <?php // main page of admin with server status
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            echo '<p class="card-text">SERVER: <strong><span class="online-text">Online</span> <span>[<a href="?admin=form&form=shutdown">SHUTDOWN</a>]</span></strong></p>';

            // get services list from /services-list.php file in web root
            $services = $services_list;

            // execute all services
            foreach ($services as $value) {

                // execute separate service row
                foreach ($value as $value) {

                    // check if service is enabled
                    if ($value["enable"]) {
                        // UFW service define
                        if ($value["service_name"] == "ufw") {

                            // check if UFW running
                            if ($services_manager->is_ufw_running()) {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        } 
                        
                        // teamSpeak server define
                        elseif ($value["service_name"] == "ts3server") {

                            // check if Team speak running
                            if ($services_manager->is_process_running($value["service_name"])) {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }

                        // minecraft server define
                        elseif ($value["service_name"] == "minecraft") {

                            // check if minecraft server starting
                            if (($response_utils->is_service_online("127.0.0.1", "25565") == "Offline") && ($services_manager->is_screen_session_running("minecraft"))) {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-info">starting...</span></p>';
                            } else {
                                
                                // check if minecraft server running
                                if ($response_utils->is_service_online("127.0.0.1", "25565") == "Online") {
                                    echo '<p class="card-text">'.$value["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                                } else {
                                    echo '<p class="card-text">'.$value["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                                }
                            }
                        }
                        
                        // others services
                        else {

                            // check if service Online
                            if ($services_manager->is_service_active($value["service_name"])) {
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