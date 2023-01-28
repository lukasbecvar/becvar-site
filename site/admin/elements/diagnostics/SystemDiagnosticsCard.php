<div class="cardPhone card diagCardPhone text-white mb-3" style="margin-left: 1%; margin-right: 1%">
    <div class="card-header diagnostics-large-title">System diagnostics</div>
    <div class="card-body diagnostics-large">
        <?php // system checks
        
            // print system compatibility test
            if (!$dashboardController->isSystemLinux()) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>unsupported host system was detected, it is possible that some components will not be functional, please consider using a linux system</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>Linux system detected</strong></span></p>';
            }

            // print Used disk space test
            if ($dashboardController->getDrivesInfo() > 89) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>main storage is full, please delete some unnecessary data or increase disk space</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>there is enough storage space on the disk</strong></span></p>';
            }

            // print CPU usage test
            if ($dashboardController->getCPUProc() > 99.00) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>CPU is overloaded, please check usage</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>CPU is at normal values and has additional processing power available</strong></span></p>';
            }

            // print RAM usage test
            if ($dashboardController->getMemoryInfo()["used"] > 99.00) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>RAM Memory is overloaded, please check usage</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>RAM Memory is available</strong></span></p>';
            }

            // print main service dir test
            if (!file_exists($pageConfig->getValueByName('serviceDir'))) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>directory: '.$pageConfig->getValueByName('serviceDir').' not exist</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>directory: '.$pageConfig->getValueByName('serviceDir').' was initialized successfully</strong></span></p>';
            }
        ?>
    </div>
</div>