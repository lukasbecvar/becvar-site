<ul class="breadcrumb">
    <li>
        <a class="selector-button btn-small" href="?admin=dbBrowser&name=visitors&limit=<?= $config->get_value("row-in-table-limit") ?>&startby=0"><strong>Unfiltered</strong></a>
    </li>  
    
    <li>
        <a class="selector-button btn-small" href="?admin=visitors&action=deleteVisitors&limit=<?= $config->get_value("row-in-table-limit") ?>&startby=0"><strong>Delete all</strong></a>
    </li>    
    
    <li class="count-text-in-menuR">Banned visitors: <strong><?= $dashboard_manager->get_banned_count() ?></strong></li>
</ul>