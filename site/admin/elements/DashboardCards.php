<div class="col-md-10 ">
    <div class="row ">
        
        <!-- logs counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-cherry">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=logReader&limit=<?php echo $config->getValue("row-in-table-limit"); ?>&startby=0" class="stats-link">Logs</a> <a href="?process=disableLogsForMe">.</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getUnreadedLogs(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- inbox counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=inbox" class="stats-link">Inbox</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getMSGSCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- todos counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-green-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=todos" class="stats-link">Todos</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getTodosCount(); ?>
                            </h2>
                         </div>
                     </div>
                </div>
            </div>
        </div>            
        
        <!-- images counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-orange-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=mediaBrowser&limit=<?php echo $config->getValue("images-in-browser-limit"); ?>&startby=0" class="stats-link">Images</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getImagesCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- pastes counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-orange-darker">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=dbBrowser&name=pastes&limit=<?php echo $config->getValue("row-in-table-limit"); ?>&startby=0" class="stats-link">Pastes</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getPastesCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- visitors counter -->
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-cyan-darker">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=visitors&limit=<?php echo $config->getValue("row-in-table-limit"); ?>&startby=0" class="stats-link">Visitors</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="text-white d-flex align-items-center mb-0">
                                <?php echo $dashboardManager->getVisitorsCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- server uptime -->
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0 text-white">Server uptime</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="text-white d-flex align-items-center mb-3">
                                <?php echo $dashboardManager->getUpTime(); ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- cpu usage -->
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0 text-white">CPU usage[CORE/AVG]</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="text-white d-flex align-items-center mb-0">
                                <span><?php echo $dashboardManager->getCPUProc(); ?>%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardManager->getCPUProc(); ?>%" aria-valuenow="<?php echo $dashboardManager->getCPUProc(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardManager->getCPUProc(); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>     
        
        <!-- ram usage -->
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0 text-white">Memory usage[RAM]</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="text-white d-flex align-items-center mb-0">
                                <span><?php echo $dashboardManager->getMemoryInfo()["used"]; ?>%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardManager->getMemoryInfo()["used"]; ?>%" aria-valuenow="<?php echo $dashboardManager->getMemoryInfo()["used"]; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardManager->getMemoryInfo()["used"]; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>    
        
        <!-- used disk space card -->
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0 text-white">Used disk space</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="text-white d-flex align-items-center mb-0">
                                <span><?php echo $dashboardManager->getDrivesInfo()."%"; ?></span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardManager->getDrivesInfo()."%"; ?>" aria-valuenow="<?php echo $dashboardManager->getDrivesInfo()."%"; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardManager->getDrivesInfo()."%"; ?>;"></div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
</div>