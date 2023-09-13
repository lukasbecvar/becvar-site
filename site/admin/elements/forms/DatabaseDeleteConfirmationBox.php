<?php 
    // get delete table name
    $table = $site_manager->get_query_string("delete");

    // get table row limit
    $row_limit = $config->get_value("row-in-table-limit");
?>

<div class="confirmation dark-table">
    <p class="login-form-title">Are you sure you want to delete <?= $table ?> ?</p>
    <a class="confirmation-button" href="?admin=dbBrowser&delete=<?= $_GET["delete"]; ?>&id=all&confirm=yes">Yes</a>
    <a class="confirmation-button" href="?admin=dbBrowser&name=<?= $table ?>&limit=<?= $row_limit ?>&startby=0">No</a>
</div>