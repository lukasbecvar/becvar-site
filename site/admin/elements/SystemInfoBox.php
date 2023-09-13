<div class="card-phone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">System info</div>
    <div class="card-body">
        <p class="card-text">OS: <?= str_replace("DISTRIB_ID=", "", $dashboard_manager->get_software_info()["distro"]["operating_system"]) ?></p>
        <p class="card-text">Kernel: <?= $dashboard_manager->get_software_info()["distro"]["kernal_version"] ?></p>
        <p class="card-text">Arch: <?= $dashboard_manager->get_software_info()["distro"]["kernal_arch"] ?></p>
    </div>
</div>