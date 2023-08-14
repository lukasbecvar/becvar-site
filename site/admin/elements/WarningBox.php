<div class="cardPhone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Warnings</div>
    <div class="card-body">
        <?php
         
            /* 
                !!!!-- //////////////////////////////////// WARNING //////////////////////////////////// --!!!!
                !!!!-- if you add new warning you must add to in $dashboardController > isWarninBoxEmpty --!!!!
            */

            // print if services dir not exist on the server
            if (!file_exists($pageConfig->getValueByName('serviceDir'))) {
                echo '<p class="card-text"><span class="text-red"><strong>Directory: '.$pageConfig->getValueByName('serviceDir').' not exist</strong></span></p>';
            }

            // print if site loaded on non https
            if (!$mainUtils->isSSL()) {
                echo '<p class="card-text"><span class="text-red"><strong>Your session is running on http [non secure connction] please contact web admin for fix it</strong></span></p>';
            }

            // print if maintenance is enabled
            if ($pageConfig->getValueByName("maintenance") == "enabled") {
                echo '<p class="card-text"><span class="text-red"><strong>Maintenance is enabled!</strong></span></p>';
            }

            // print if dev-mode is enabled
            if ($siteController->isSiteDevMode()) {
                echo '<p class="card-text"><span class="text-red"><strong>Developer mode is enabled!</strong></span></p>';
            }

            // print Used disk space == 90%
            if ($dashboardController->getDrivesInfo() > 89) {
                echo '<p class="card-text"><span class="text-red"><strong>Used disk space is more than 90% please try clean the file system</strong></span></p>';
            }
            
            // print anti log warning
            if (empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")])) {
                echo '<p class="card-text text-warning"><strong>Logging for your browser is enabled you can disable <a href="?process=disableLogsForMe">here</a></strong></p>';
            }

            // print new logs warning
            if (($dashboardController->getUnreadedLogs()) != "0" && (!empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")]))) {
                echo '<p class="card-text text-warning"><strong>New logs found you can see it <a href="?admin=logReader&limit='.$pageConfig->getValueByName("rowInTableLimit").'&startby=0">here</a></strong></p>';
            }

            // print new messages
            if ($dashboardController->getMSGSCount() != "0") {
                echo '<p class="card-text text-warning"><strong>New messages found you can see it <a href="?admin=inbox">here</a></strong></p>';
            }

            // print if no warnings
            if ($dashboardController->isWarninBoxEmpty()) {
                echo 'No warnings found.';
            }
        ?>
    </div>
</div>