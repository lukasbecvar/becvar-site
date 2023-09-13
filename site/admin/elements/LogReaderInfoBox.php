<div class="card text-white mb-0" style="margin: 0; border-top: 1px solid rgba(255, 255, 255, 0.24); border-bottom: 1px solid rgba(255, 255, 255, 0.24);">
<div class="card-header">Basic info</div>
    <div class="card-body">
    <p class="card-text"><strong>Logs count: <span class="text-primary"><?= $dashboard_manager->get_logs_count() ?></span> / unreaded: <span class="text-primary"><?php echo $dashboard_manager->get_unreaded_logs(); ?></span></strong></strong></p>
        <p class="card-text"><strong>Login, Logout: <span class="text-primary"><?= $dashboard_manager->get_login_logs_count() ?></span></strong></p>
    </div>
</div>