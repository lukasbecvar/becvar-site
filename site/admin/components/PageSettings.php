<div class="dashboardBox">
    <?php 
        // check if config change submited
        if ($siteController->getQueryString("config") != null) {

            // get config action from url
            $config = $siteController->getQueryString("config");

            //////////////////////////////////////////////////////////
            // maintenance settings change requests
            if ($config == "maintenanceDisable") {
                $configController->maintenanceDisable();

            } elseif ($config == "maintenanceEnable") {
                $configController->maintenanceEnable();
            

            // dev mode settings change requests
            } elseif ($config == "devmodeDisable") {
                $configController->devModeDisable();
            
            } elseif ($config == "devmodeEnable") {
                $configController->devModeEnable();
            }
            //////////////////////////////////////////////////////////
            
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