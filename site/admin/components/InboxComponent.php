<div class="adminPanel">
    <center>
        <?php // admin inbox component (for show msgs from contact site)
        
            // check if user typed id
            if (isset($_GET["delete"])) {

                // get and escape string id form url and save to id
                $id = $mysqlUtils->escapeString($_GET["delete"], true, true);

                // delete msg by id
                $contactController->deleteMsgByID($id);

                // redirect to messages page
                $urlUtils->jsRedirect("?admin=inbox");
            } 
         
            // print msgs is not empty
            if ($contactController->isEmpty()) {
                echo"<h2 class=pageTitle>Inbox is empty</h2>";
            } else {
                $contactController->printMSGS();
            }
        ?><br>
    </center>
</div>