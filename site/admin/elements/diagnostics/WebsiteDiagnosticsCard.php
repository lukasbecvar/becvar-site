<div class="cardPhone card text-white bg-dark mb-3" style="margin-left: 1%; margin-right: 1%">
    <div class="card-header">Website diagnostics</div>
    <div class="card-body">
        <?php //System checks
        
            //Print subdomain test
            if (str_starts_with($_SERVER['HTTP_HOST'], "www")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>the page runs on a subdomain, please remove subdomain form config only like domain.name</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>subdomain was not detected</strong></span></p>';
            }

            //Print dev mode test
            if (($_SERVER['HTTP_HOST'] != "localhost") && $pageConfig->getValueByName("dev_mode")) {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-red"><i class="fa fa-exclamation-triangle"></i> </span>developer mode is enabled on non localhost site, please disable dev_mode in config.php</strong></span></p>';
            } else {
                echo '<p class="card-text"><span class="text-warning"><strong><span class="text-light-green"><i class="fa fa-check"></i> </span>developer mode policy are OK</strong></span></p>';
            }
        ?>
    </div>
</div>