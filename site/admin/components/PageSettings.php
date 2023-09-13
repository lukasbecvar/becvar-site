<div class="admin-panel">
    <?php 
        // check if config change submited
        if ($site_manager->get_query_string("config") != null) {

            // get config action from url
            $config_value = $site_manager->get_query_string("config");
 
            //////////////////////////////////////////////////////////
            // maintenance settings change requests
            if ($config_value == "maintenanceDisable") {
                $config->update_maintenance("disabled");

            } elseif ($config_value == "maintenanceEnable") {
                $config->update_maintenance("enabled");
            

            // dev mode settings change requests
            } elseif ($config_value == "devmodeDisable") {
                $config->update_dev_mode(false);
            
            } elseif ($config_value == "devmodeEnable") {
                $config->update_dev_mode(true);
            }
            //////////////////////////////////////////////////////////
            
            // sleep for 3s (wait until the value is written to the config)
            sleep(3);

            // refresh page
            $url_utils->js_redirect("?admin=pageSettings"); 
        }
    ?>
    <h1 class="page-title display-4">Page settings</h1>
    <?php
        // settings box
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/PageSettingsBox.php');
    ?>
</div>