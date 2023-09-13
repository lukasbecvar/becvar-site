<p class="settings-value-box">
    Maintenance [<?= $config->get_value('maintenance') ?>]
    <a class="settings-button" href="?admin=pageSettings&config=maintenanceDisable">Disable</a>
    <a class="settings-button" href="?admin=pageSettings&config=maintenanceEnable">Enable</a>
</p>

<p class="settings-value-box">
    Dev mode [<?php
        if ($config->get_value('dev-mode')) {
            echo "enabled";
        } else {
            echo "disabled";
        }
    ?>]
    <a class="settings-button" href="?admin=pageSettings&config=devmodeDisable">Disable</a>
    <a class="settings-button" href="?admin=pageSettings&config=devmodeEnable">Enable</a>
</p>