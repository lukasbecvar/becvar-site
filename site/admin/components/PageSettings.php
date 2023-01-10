<div class="dashboardBox">
    <?php 
        // check if config change submited
        if (isset($_GET["config"])) {

            // get config action from url
            $config = $mysqlUtils->escapeString($_GET["config"], true, true);

            //////////////////////////////////////////////////////////
            // maintenance settings change requests
            if ($config == "maintenanceDisable") {
                $pageConfigController->maintenanceDisable();

            } elseif ($config == "maintenanceEnable") {
                $pageConfigController->maintenanceEnable();
            

            // dev mode settings change requests
            } elseif ($config == "devmodeDisable") {
                $pageConfigController->devModeDisable();
            
            } elseif ($config == "devmodeEnable") {
                $pageConfigController->devModeEnable();
            

            // API settings change requests
            } elseif ($config == "apiEnable") {
                $pageConfigController->apiEnable();
            } elseif ($config == "apiDisable") {
                $pageConfigController->apiDisable();
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