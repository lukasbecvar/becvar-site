<div class="admin-panel ooverflow-x-hiden">
<?php // main admin component for include all dashboard elements

    // warnning box
    if (!$dashboard_manager->is_warnin_box_empty()) {
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/WarningBox.php');
    }

    // services status element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/ServiceStatusBox.php');

    // system info element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/SystemInfoBox.php');

    // card element
    include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/DashboardCards.php');
?>
<style>
    .wrapper .section .top-navbar {
        width: calc(100% - 240px);
    }
</style>
</div>