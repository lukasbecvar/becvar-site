<div class="card-phone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Warnings</div>
    <div class="card-body">
        <?php
         
            /* 
                !!!!-- //////////////////////////////////// WARNING //////////////////////////////////// --!!!!
                !!!!-- if you add new warning you must add to in $dashboard_manager > is_warnin_box_empty --!!!!
            */

            // print if services dir not exist on the server
            if (!file_exists($config->get_value('service-dir'))) {
                echo '<p class="card-text"><span class="text-red"><strong>Directory: '.$config->get_value('service-dir').' not exist</strong></span></p>';
            }

            // print if site loaded on non https
            if (!$main_utils->is_ssl()) {
                echo '<p class="card-text"><span class="text-red"><strong>Your session is running on http [non secure connction] please contact web admin for fix it</strong></span></p>';
            }

            // print if maintenance is enabled
            if ($config->get_value("maintenance") == "enabled") {
                echo '<p class="card-text"><span class="text-red"><strong>Maintenance is enabled!</strong></span></p>';
            }

            // print if dev-mode is enabled
            if ($site_manager->is_dev_mode()) {
                echo '<p class="card-text"><span class="text-red"><strong>Developer mode is enabled!</strong></span></p>';
            }

            // print Used disk space == 90%
            if ($dashboard_manager->get_drive_usage() > 89) {
                echo '<p class="card-text"><span class="text-red"><strong>Used disk space is more than 90% please try clean the file system</strong></span></p>';
            }
            
            // print anti log warning
            if (empty($_COOKIE[$config->get_value("anti-log-cookie")])) {
                echo '<p class="card-text text-warning"><strong>Logging for your browser is enabled you can disable <a href="?process=disableLogsForMe">here</a></strong></p>';
            }

            // print new logs warning
            if (($dashboard_manager->get_unreaded_logs()) != "0" && (!empty($_COOKIE[$config->get_value("anti-log-cookie")]))) {
                echo '<p class="card-text text-warning"><strong>New logs found you can see it <a href="?admin=logReader&limit='.$config->get_value("row-in-table-limit").'&startby=0">here</a></strong></p>';
            }

            // print new messages
            if ($dashboard_manager->get_messages_count() != "0") {
                echo '<p class="card-text text-warning"><strong>New messages found you can see it <a href="?admin=inbox">here</a></strong></p>';
            }

            // print if no warnings
            if ($dashboard_manager->is_warnin_box_empty()) {
                echo 'No warnings found.';
            }
        ?>
    </div>
</div>