<div class="adminPanel">
    <?php 
        // check if config change submited
        if ($siteManager->getQueryString("config") != null) {

            // get config action from url
            $configValue = $siteManager->getQueryString("config");
 
            //////////////////////////////////////////////////////////
            // maintenance settings change requests
            if ($configValue == "maintenanceDisable") {
                $config->updateMaintenanceValue("disabled");

            } elseif ($configValue == "maintenanceEnable") {
                $config->updateMaintenanceValue("enabled");
            

            // dev mode settings change requests
            } elseif ($configValue == "devmodeDisable") {
                $config->updateDevModeValue(false);
            
            } elseif ($configValue == "devmodeEnable") {
                $config->updateDevModeValue(true);
            }
            //////////////////////////////////////////////////////////
            
            // sleep for 3s (wait until the value is written to the config)
            sleep(3);

            // refresh page
            $urlUtils->jsRedirect("?admin=pageSettings"); 
        }
    ?>
    <h1 class="pageTitle display-4">Page settings</h1>
    <?php
        // settings box
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/PageSettingsBox.php');
    ?>
</div>