<ul class="breadcrumb bg-dark">
    <li>
        <a class="selectorButton btn-small" href="?admin=dbBrowser&name=visitors&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0"><strong>Unfiltered browser</strong></a>
    </li>  
    
    <li>
        <a class="selectorButton btn-small" href="?admin=visitors&action=deleteVisitors&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0"><strong>Delete all</strong></a>
    </li>    
    
    <li class="countTextInMenuR">Visitors manager</li>
</ul>