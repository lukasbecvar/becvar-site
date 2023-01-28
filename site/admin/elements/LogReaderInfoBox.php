<div class="card text-white mb-4" style="margin-left: 10px; margin-right: 10px;">
<div class="card-header">Basic info</div>
    <div class="card-body">
    <p class="card-text"><strong>Logs count: <span class="text-primary"><?php echo $dashboardController->getLogsCount(); ?></span> / unreaded: <span class="text-primary"><?php echo $dashboardController->getUnreadedLogs(); ?></span></strong></strong></p>
        <p class="card-text"><strong>Login, Logout: <span class="text-primary"><?php echo $dashboardController->getLoginLogsCount(); ?></span></strong></p>
    </div>
</div>