<?php // admin dashboard form manager

    // check if form is shutdown
    if ($siteController->getQueryString("form") == "shutdown") {
        include_once("elements/forms/EmergencyShutdownConfirmation.php");
    }
?>