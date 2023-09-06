<?php // admin dashboard form handlerer

    // check if form is shutdown
    if ($site_manager->get_query_string("form") == "shutdown") {
        include_once("elements/forms/EmergencyShutdownConfirmation.php");
    }
?>