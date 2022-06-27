<ul class="breadcrumb bg-dark">
    <li>
        <a class="selectorButton btn-small" href="?page=admin&process=dbBrowser&name=logs&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0"><strong>Unfiltered browser</strong></a>
    </li>  
 
    <li>
        <a class="selectorButton btn-small" href="?page=admin&process=logReader&action=setReaded&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0"><strong>Readed all</strong></a>
    </li> 
    
    <li>
        <a class="selectorButton btn-small" href="?page=admin&process=logReader&action=deleteLogs&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0"><strong>Delete all</strong></a>
    </li>    
    
    <li class="countTextInMenuR">Logs reader</li>
</ul>