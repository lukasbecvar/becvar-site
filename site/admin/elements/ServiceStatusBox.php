<div class="card-phone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Service status</div>
    <div class="card-body"> 
        <?php // main page of admin with server status
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            echo '<p class="card-text">SERVER: <strong><span class="online-text">Online</span> <span>[<a href="?admin=form&form=shutdown">SHUTDOWN</a>]</span></strong></p>';

            // get services list from /services-list.php file in web root
            $services = $json_utils->get_json_from(__DIR__."/../../../services-list.json");

            // check if services list load valid
            if ($services != null) {
                // execute separate service row
                foreach ($services as $index => $value) {

                    // check if service is enabled
                    if ($services[$index]["enable"]) {
                        // UFW service define
                        if ($services[$index]["service_name"] == "ufw") {

                            // check if UFW running
                            if ($services_manager->is_ufw_running()) {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        } 
                            
                        // teamSpeak server define
                        elseif ($services[$index]["service_name"] == "ts3server") {

                            // check if Team speak running
                            if ($services_manager->is_process_running($services[$index]["service_name"])) {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }

                        // minecraft server define
                        elseif ($services[$index]["service_name"] == "minecraft") {

                            // check if minecraft server starting
                            if (($response_utils->is_service_online("127.0.0.1", "25565") == "Offline") && ($services_manager->is_screen_session_running("minecraft"))) {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <span class="text-info">starting...</span></p>';
                            } else {
                                    
                                // check if minecraft server running
                                if ($response_utils->is_service_online("127.0.0.1", "25565") == "Online") {
                                    echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                                } else {
                                    echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="text-warning">Offline</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Start">START</a>]</span></strong></p>';
                                }
                            }
                        }
                            
                        // others services
                        else {

                            // check if service Online
                            if ($services_manager->is_service_active($services[$index]["service_name"])) {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="online-text">Online</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$services[$index]["display_name"].': <strong><span class="text-red">Offline</span> <span>[<a href="?admin=executeTask&command='.$services[$index]["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }
                    }
                }
            } else {

                // log error
                $mysql->log("app-error", "error to get services-list.json file, try check app root if file exist");
            } 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ?>
    </div>
</div>