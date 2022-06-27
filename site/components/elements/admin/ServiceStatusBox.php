<div class="cardPhone card text-white bg-dark mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Service status</div>
    <div class="card-body"> 
        <?php //The main page of admin with server status


            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo '<p class="cardSubTitle">Main services</p>';

            //Get UFW status
            if ($servicesController->isServiceInstalled("ufw")) {

                if ($servicesController->isUFWRunning()) {
                    echo '<p class="card-text">UFW[Firewall]: <span class="text-success">enabled</span> <strong><span>[<a href="?page=admin&process=executeTask&command=ufwStart">START</a>, <a href="?page=admin&process=executeTask&command=ufwStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">UFW[Firewall]: <span class="text-warning">disabled</span> <strong><span>[<a href="?page=admin&process=executeTask&command=ufwStart">START</a>, <a href="?page=admin&process=executeTask&command=ufwStop">STOP</a>]</span></strong></p>';
                }

            } 

            //Get SSH status
            if ($servicesController->isServiceInstalled("ssh.service")) {
                if ($servicesController->ifServiceActive("ssh.service")) {
                    echo '<p class="card-text">SSH: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=opensshStart">START</a>, <a href="?page=admin&process=executeTask&command=opensshStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">SSH: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=opensshStart">START</a>, <a href="?page=admin&process=executeTask&command=opensshStop">STOP</a>]</span></strong></p>';
                }
            }

            //Get openvpn status
            if ($servicesController->isServiceInstalled("openvpn")) {
                if ($servicesController->ifServiceActive("openvpn")) {
                    echo '<p class="card-text">OpenVPN: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=openvpnStart">START</a>, <a href="?page=admin&process=executeTask&command=openvpnStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">OpenVPN: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=openvpnStart">START</a>, <a href="?page=admin&process=executeTask&command=openvpnStop">STOP</a>]</span></strong></p>';
                }
            }

            //Get apache status
            if ($servicesController->isServiceInstalled("apache2")) {
                if ($servicesController->ifServiceActive("apache2")) {
                    echo '<p class="card-text">Apache2: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=apacheStart">START</a>, <a href="?page=admin&process=executeTask&command=apacheStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">Apache2: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=apacheStart">START</a>, <a href="?page=admin&process=executeTask&command=apacheStop">STOP</a>]</span></strong></p>';
                }
            }

            //Get mariadb status
            if ($servicesController->isServiceInstalled("mariadb")) {
                if ($servicesController->ifServiceActive("mariadb")) {
                    echo '<p class="card-text">MariaDB: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=mariadbStart">START</a>, <a href="?page=admin&process=executeTask&command=mariadbStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">MariaDB: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=mariadbStart">START</a>, <a href="?page=admin&process=executeTask&command=mariadbStop">STOP</a>]</span></strong></p>';
                }
            }

            //Get tor status
            if ($servicesController->isServiceInstalled("tor")) {
                if ($servicesController->ifServiceActive("tor")) {
                    echo '<p class="card-text">Tor: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=torStart">START</a>, <a href="?page=admin&process=executeTask&command=torStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">Tor: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=torStart">START</a>, <a href="?page=admin&process=executeTask&command=torStop">STOP</a>]</span></strong></p>';
                }
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo '<p class="cardSubTitle">Screen services</p>';

            //Get minecraft status
            if ($servicesController->isServiceInstalled("minecraft")) {
                if ($responseUtils->serviceOnlineCheck("127.0.0.1", "25565") == "Online") {
                    echo '<p class="card-text">Minecraft: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=minecraftStart">START</a>, <a href="?page=admin&process=executeTask&command=minecraftStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">Minecraft: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=minecraftStart">START</a>, <a href="?page=admin&process=executeTask&command=minecraftStop">STOP</a>]</span></strong></p>';
                }
            } 

            //Get dubinek status
            if ($servicesController->isServiceInstalled("dubinek")) {
                if ($servicesController->checkScreenSession("dubinek") == true) {
                    echo '<p class="card-text">Dubinek: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=dubinekStart">START</a>, <a href="?page=admin&process=executeTask&command=dubinekStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">Dubinek: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=dubinekStart">START</a>, <a href="?page=admin&process=executeTask&command=dubinekStop">STOP</a>]</span></strong></p>';
                }
            } 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////








            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo '<p class="cardSubTitle">Others services</p>';

            //Get team speak status
            if ($servicesController->isServiceInstalled("ts3server")) {
                if ($servicesController->ifProcessRunning("ts3server")) {
                    echo '<p class="card-text">TeamSpeak: <span class="text-success">online</span> <strong><span>[<a href="?page=admin&process=executeTask&command=ts3serverStart">START</a>, <a href="?page=admin&process=executeTask&command=ts3serverStop">STOP</a>]</span></strong></p>';
                } else {
                    echo '<p class="card-text">TeamSpeak: <span class="text-warning">offline</span> <strong><span>[<a href="?page=admin&process=executeTask&command=ts3serverStart">START</a>, <a href="?page=admin&process=executeTask&command=ts3serverStop">STOP</a>]</span></strong></p>';
                }
            } 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ?>
    </div>
</div>
