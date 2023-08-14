<?php // services list for dashboard panel

    namespace becwork\services;

    class ServicesManager {

        public $services = [

            // SSHD service
            "sshd" => [
                "service_name" => "sshd",
                "display_name" => "SSHD",
                "start_cmd" => "sudo systemctl start sshd",
                "stop_cmd" => "sudo systemctl stop sshd",
                "enable" => true                
            ],

            // uncomplicated Firewall service 
            "ufw" => [
                "service_name" => "ufw",
                "display_name" => "UFW-[Firewall]",
                "start_cmd" => "sudo ufw enable",
                "stop_cmd" => "sudo ufw disable",
                "enable" => true
            ],

            // mysql service
            "mysql" => [
                "service_name" => "mysql",
                "display_name" => "Mysql",
                "start_cmd" => "sudo systemctl start mysql",
                "stop_cmd" => "sudo systemctl stop mysql",
                "enable" => true                
            ]
        ];
    }
?>
