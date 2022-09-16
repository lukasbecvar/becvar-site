<div class="cardPhone card text-white bg-dark mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Service status</div>
    <div class="card-body"> 
        <?php //The main page of admin with server status
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //Get services list from /services-list.php file in web root
            $services = $servicesList;

            //Execute all services
            foreach ($services as $value) {

                //Execute separate service row
                foreach ($value as $value) {

                    //Check if service is enabled
                    if ($value["enable"]) {
                        //UFW service define
                        if ($value["service_name"] == "ufw") {

                            //Check if UFW running
                            if ($servicesController->isUFWRunning()) {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-success">enabled</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-warning">disabled</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        } 
                        
                        //TeamSpeak server define
                        elseif ($value["service_name"] == "ts3server") {

                            //Check if Team speak running
                            if ($servicesController->ifProcessRunning($value["service_name"])) {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-success">online</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-warning">offline</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }

                        //TeamSpeak server define
                        elseif ($value["service_name"] == "minecraft") {

                            //Check if minecraft server is running
                            if ($responseUtils->serviceOnlineCheck("127.0.0.1", "25565") == "Online") {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-success">online</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-warning">offline</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }
                        
                        //Others services
                        else {
                            if ($servicesController->ifServiceActive($value["service_name"])) {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-success">online</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Stop">STOP</a>]</span></strong></p>';
                            } else {
                                echo '<p class="card-text">'.$value["display_name"].': <span class="text-warning">offline</span> <strong><span>[<a href="?admin=executeTask&command='.$value["service_name"].'_Start">START</a>]</span></strong></p>';
                            }
                        }
                    }
                }
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ?>
    </div>
</div>
