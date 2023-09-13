<?php 
    $limit = $config->get_value("row-in-table-limit");
?>
<ul class="breadcrumb"> 
    <li>
        <a class="selector-button btn-small" href="?admin=dbBrowser&name=logs&limit=<?= $limit ?>&startby=0"><strong>Unfiltered</strong></a>
    </li>  
 
    <li>
        <a class="selector-button btn-small" href="?admin=logReader&action=setReaded&limit=<?= $limit ?>&startby=0"><strong>Readed all</strong></a>
    </li> 
    
    <li>
        <a class="selector-button btn-small" href="?admin=logReader&action=deleteLogs&limit=<?= $limit ?>&startby=0"><strong>Delete all</strong></a>
    </li>    
    
    <li class="count-text-in-menuR">Logs reader</li>
</ul>