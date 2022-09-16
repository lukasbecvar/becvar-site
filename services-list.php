<?php //Services list for dashboard panel

    class ServicesManager {

        public $services = [

            //Uncomplicated Firewall service 
            "ufw" => [
                "service_name" => "ufw",
                "display_name" => "UFW[Firewall]",
                "start_cmd" => "sudo ufw enable",
                "stop_cmd" => "sudo ufw disable",
                "enable" => true
            ],

            //TeamSpeak server
            "ts3server" => [
                "service_name" => "ts3server",
                "display_name" => "TeamSpeak",
                "start_cmd" => "sudo sh /services/teamspeak/ts3server_startscript.sh start",
                "stop_cmd" => "sudo sh /services/teamspeak/ts3server_startscript.sh stop",
                "enable" => true                
            ],

            //Minecraft server
            "minecraft" => [
                "service_name" => "minecraft",
                "display_name" => "Minecraft",
                "start_cmd" => "sudo sh /services/minecraft/srv_start.sh",
                "stop_cmd" => "sudo sh /services/minecraft/srv_stop.sh",
                "enable" => true                
            ],

            //OpenVPN service
            "openvpn" => [
                "service_name" => "openvpn",
                "display_name" => "OpenVPN",
                "start_cmd" => "sudo systemctl start openvpn",
                "stop_cmd" => "sudo systemctl stop openvpn",
                "enable" => true                
            ],

            //Apache2 service
            "apache2" => [
                "service_name" => "apache2",
                "display_name" => "Apache2",
                "start_cmd" => "sudo systemctl start apache2",
                "stop_cmd" => "sudo systemctl stop apache2",
                "enable" => true                
            ],

            //Mariadb service
            "mariadb" => [
                "service_name" => "mariadb",
                "display_name" => "MariaDB",
                "start_cmd" => "sudo systemctl start mariadb",
                "stop_cmd" => "sudo systemctl stop mariadb",
                "enable" => true                
            ],
            
            //SSHD service
            "sshd" => [
                "service_name" => "sshd",
                "display_name" => "SSHD",
                "start_cmd" => "sudo systemctl start sshd",
                "stop_cmd" => "sudo systemctl stop sshd",
                "enable" => true                
            ],

            //Tor service
            "tor" => [
                "service_name" => "tor",
                "display_name" => "Tor",
                "start_cmd" => "sudo systemctl start tor",
                "stop_cmd" => "sudo systemctl stop tor",
                "enable" => true                
            ]
        ];
    }
?>