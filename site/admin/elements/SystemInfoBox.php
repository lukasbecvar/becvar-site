<div class="cardPhone card text-white mb-3" style="margin-left: 13px; margin-right: 17.5%">
    <div class="card-header">System info</div>
    <div class="card-body">
        <p class="card-text">OS: <?php echo str_replace("DISTRIB_ID=", "", $dashboardController->getSoftwareInfo()["distro"]["operating_system"]); ?></p>
        <p class="card-text">Kernel: <?php echo $dashboardController->getSoftwareInfo()["distro"]["kernal_version"]; ?></p>
        <p class="card-text">Arch: <?php echo $dashboardController->getSoftwareInfo()["distro"]["kernal_arch"]; ?></p>
    </div>
</div>