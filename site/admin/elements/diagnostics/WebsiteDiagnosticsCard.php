<div class="card-phone card diag-card-phone text-white mb-3" style="margin-left: 1%; margin-right: 1%">
    <div class="card-header diagnostics-large-title">Website diagnostics</div>
    <div class="card-body diagnostics-large">
        <?php // system checks
        
            // print ssl test
            if ((!$main_utils->is_ssl() && $site_manager->get_http_host() != "localhost")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>session is running on http [non secure connction] please contact web admin for fix it</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>page is secured with https</strong></span></p>';
            }

            // print subdomain test
            if (str_starts_with($_SERVER['HTTP_HOST'], "www")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>the page runs on a subdomain, please remove subdomain form config only like domain.name</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>subdomain was not detected</strong></span></p>';
            }

            // print dev mode test
            if (($_SERVER['HTTP_HOST'] != "localhost") && $site_manager->is_dev_mode()) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>developer mode is enabled on non localhost site, please disable dev-mode in config.php</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>developer mode policy are OK</strong></span></p>';
            }

            // print maintenance test
            if ($config->get_value("maintenance") == "enabled") {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>public pages are unavailable for maintenance</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>maintenance is disabled, page is available</strong></span></p>';
            }
        ?>
    </div>
</div>