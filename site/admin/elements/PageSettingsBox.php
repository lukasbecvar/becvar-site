<p class="settingsValueBox">
    Maintenance [<?php echo $config->getValue('maintenance');?>]
    <a class="settingsButton" href="?admin=pageSettings&config=maintenanceDisable">Disable</a>
    <a class="settingsButton" href="?admin=pageSettings&config=maintenanceEnable">Enable</a>
</p>

<p class="settingsValueBox">
    Dev mode [<?php
        if ($config->getValue('dev-mode')) {
            echo "enabled";
        } else {
            echo "disabled";
        }
    ?>]
    <a class="settingsButton" href="?admin=pageSettings&config=devmodeDisable">Disable</a>
    <a class="settingsButton" href="?admin=pageSettings&config=devmodeEnable">Enable</a>
</p>