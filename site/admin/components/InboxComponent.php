<div class="adminPanel">
    <center>
        <?php // admin inbox component (for show msgs from contact site)
        
            // check if user typed id
            if ($siteManager->getQueryString("delete") != null) {

                // get and escape string id form url and save to id
                $id = $siteManager->getQueryString("delete");

                // delete msg by id
                $contactManager->deleteMsgByID($id);

                // redirect to messages page
                $urlUtils->jsRedirect("?admin=inbox");
            } 
         
            // print msgs is not empty
            if ($contactManager->isEmpty()) {
                echo"<h2 class=pageTitle>Inbox is empty</h2>";
            } else {
                $contactManager->printMSGS();
            }
        ?><br>
    </center>
</div>