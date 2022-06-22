<div class="contactPanel">
    <center>
        <?php //The admin inbox component (for show msgs from contact site)
        
            //Check if user typed id
            if (isset($_GET["delete"])) {

                //Get and escape string id form url and save to id
                $id = $mysqlUtils->escapeString($_GET["delete"], true, true);

                //Delete msg by id
                $contactController->deleteMsgByID($id);

                //Redirect to messages page
                $urlUtils->jsRedirect("index.php?page=admin&process=inbox");
            } 
         
            //Print msgs is not empty
            if ($contactController->isEmpty()) {
                echo"<h2 class=pageTitle>Inbox is empty</h2>";
            } else {
                $contactController->printMSGS();
            }
        ?><br>
    </center>
</div>
