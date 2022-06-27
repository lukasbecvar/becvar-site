<div class="dashboardBox">
    <?php 
        //Check if config change submited
        if (isset($_GET["config"])) {

            //Get config action from url
            $config = $mysqlUtils->escapeString($_GET["config"], true, true);

            //Make action by value
            //Maintenance
            if ($config == "maintenanceDisable") {
                $pageConfigController->maintenanceDisable();

            } elseif ($config == "maintenanceEnable") {
                $pageConfigController->maintenanceEnable();
            

            //Dev mode
            } elseif ($config == "devmodeDisable") {
                $pageConfigController->devModeDisable();
            
            } elseif ($config == "devmodeEnable") {
                $pageConfigController->devModeEnable();
            

            //API
            } elseif ($config == "apiEnable") {
                $pageConfigController->apiEnable();
            } elseif ($config == "apiDisable") {
                $pageConfigController->apiDisable();
            }

            //Refresh page
            $urlUtils->jsRedirect("?page=admin&process=pageSettings"); 
        }
    ?>
    <h1 class="pageTitle display-4">Page settings</h1>
    <?php
        //Include settings box
        include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/admin/PageSettingsBox.php');
    ?>
</div>