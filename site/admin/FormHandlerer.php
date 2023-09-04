<?php // admin dashboard form handlerer

    // check if form is shutdown
    if ($siteManager->getQueryString("form") == "shutdown") {
        include_once("elements/forms/EmergencyShutdownConfirmation.php");
    }
?>