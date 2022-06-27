<div class="cardPhone card text-white bg-dark mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">Warnings</div>
    <div class="card-body">
        <?php 
            //Print if services dir not exist on the server
            if (!file_exists($pageConfig->getValueByName('serviceDir'))) {  
                echo '<p class="card-text"><span class="text-red"><strong>Directory: '.$pageConfig->getValueByName('serviceDir').' not exist</strong></span></p>';
            }



            //Print if site loaded on non https
            if ((!$mainUtils->isSSL() && $siteController->getHTTPhost() != "localhost")) {
                echo '<p class="card-text"><span class="text-red"><strong>Your session is running on http [non secure connction] please contact web admin for fix it</strong></span></p>';
            }



            //Print Used disk space == 90%
            if ($dashboardController->getDrivesInfo() > 89) {
                echo '<p class="card-text"><span class="text-red"><strong>Used disk space is more than 90% please try clean the file system</strong></span></p>';
            }
            
            
            
            //Print anti log warning
            if (empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")])) {
                echo '<p class="card-text text-warning"><strong>Logging for your browser is enabled you can disable <a href="?page=admin&process=disableLogsForMe">here</a></strong></p>';
            }



            //Print new logs warning
            if (($dashboardController->getUnreadedLogs()) != "0" && (!empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")]))) {
                echo '<p class="card-text text-warning"><strong>New logs found you can see it <a href="?page=admin&process=logReader&limit='.$pageConfig->getValueByName("rowInTableLimit").'&startby=0">here</a></strong></p>';
            }



            //Print new messages
            if ($dashboardController->getMSGSCount() != "0") {
                echo '<p class="card-text text-warning"><strong>New messages found you can see it <a href="?page=admin&process=inbox">here</a></strong></p>';
            }



            //Print if no warnings
            if (
                !empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")]) &&
                !($dashboardController->getUnreadedLogs()) != "0" && (!empty($_COOKIE[$pageConfig->getvalueByName("antiLogCookie")])) &&
                !$dashboardController->getMSGSCount() != "0" &&
                file_exists($pageConfig->getValueByName('serviceDir')) &&
                $dashboardController->getDrivesInfo() < 89 &&
                !(!$mainUtils->isSSL() && $siteController->getHTTPhost() != "localhost")
            ) {
                echo 'No warnings found.';
            }
        ?>
    </div>
</div>