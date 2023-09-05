<?php 
    // get delete table name
    $table = $siteManager->getQueryString("delete");

    // get table row limit
    $row_limit = $config->getValue("rowInTableLimit");
?>

<div class="confirmation dark-table">
    <p class="loginFormTitle">Are you sure you want to delete <?php echo $table ?> ?</p>
    <a class="confirmationButton" href="?admin=dbBrowser&delete=<?php echo $_GET["delete"]; ?>&id=all&confirm=yes">Yes</a>
    <a class="confirmationButton" href="?admin=dbBrowser&name=<?php echo $table ?>&limit=<?php echo $row_limit ?>&startby=0">No</a>
</div>