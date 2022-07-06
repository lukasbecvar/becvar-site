<div class="col-md-10 ">
    <div class="row ">
        
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-cherry">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=logReader&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0" class="stats-link">Logs</a> <a href="?process=disableLogsForMe">.</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getUnreadedLogs(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=inbox" class="stats-link">Inbox</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getMSGSCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-green-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=todos" class="stats-link">Todos</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getTodosCount(); ?>
                            </h2>
                         </div>
                     </div>
                </div>
            </div>
        </div>            
        
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-orange-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=mediaBrowser&limit=<?php echo $pageConfig->getValueByName("imagesInBrowserLimit"); ?>&startby=0" class="stats-link">Images</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getImagesCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-orange-darker">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=dbBrowser&name=pastes&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0" class="stats-link">Pastes</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getPastesCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-6">
            <div class="card l-bg-cyan-darker">
                <div class="card-statistic-3 p-4">
                    <div class="mb-2">
                        <h5 class="card-title mb-0"><a class="cardLink" href="?admin=visitors&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0" class="stats-link">Visitors</a></h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-8">
                            <h2 class="d-flex align-items-center mb-0">
                                <?php echo $dashboardController->getVisitorsCount(); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0">Server uptime</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="d-flex align-items-center mb-3">
                                <?php echo $dashboardController->getUpTime(); ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0">CPU usage[CORE/AVG]</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="d-flex align-items-center mb-0">
                                <span><?php echo $dashboardController->getCPUProc(); ?>%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardController->getCPUProc(); ?>%" aria-valuenow="<?php echo $dashboardController->getCPUProc(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardController->getCPUProc(); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>     
        
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0">Memory usage[RAM]</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="d-flex align-items-center mb-0">
                                <span><?php echo $dashboardController->getMemoryInfo()["used"]; ?>%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardController->getMemoryInfo()["used"]; ?>%" aria-valuenow="<?php echo $dashboardController->getMemoryInfo()["used"]; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardController->getMemoryInfo()["used"]; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>    
        
        <div class="col-xl-3 col-lg-6">
            <div class="card l-bg-blue-dark">
                <div class="card-statistic-3 p-4">
                    <div class="mb-3">
                        <h5 class="card-title mb-0">Used disk space</h5>
                    </div>
                    <div class="row align-items-center mb-0 d-flex">
                        <div class="col-12">
                            <h5 class="d-flex align-items-center mb-0">
                                <span><?php echo $dashboardController->getDrivesInfo()."%"; ?></span>
                            </h5>
                        </div>
                    </div>
                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar bg-red-custom" role="progressbar" data-width="<?php echo $dashboardController->getDrivesInfo()."%"; ?>" aria-valuenow="<?php echo $dashboardController->getDrivesInfo()."%"; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $dashboardController->getDrivesInfo()."%"; ?>;"></div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
</div>