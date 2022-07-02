<div class="adminPanel">
<?php //Main admin component for include all dashboard elements

    //Include warnning box
    if (!$dashboardController->isWarninBoxEmpty()) {
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/WarningBox.php');
    }

    //Include services status element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/ServiceStatusBox.php');

    //Include system info element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/SystemInfoBox.php');

    //Include card element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/DashboardCards.php');
?>
</div>