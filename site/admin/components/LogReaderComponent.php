<div class="adminPanel">
<?php //Log reader [admin component]

	//Init default values 
	$startByRow = 0;

	//Check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		//Page items limit
		$limitOnPage = $pageConfig->getValueByName("rowInTableLimit");

		//If limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			//Get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);

			//Get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);

			//Set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			//Set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}

        //include navbar
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderNavPanel.php');
        

        //Get all logs from table
        $logs = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM logs WHERE status NOT LIKE 'readed' ORDER BY id DESC LIMIT $startByRow, $limitOnPage");


        //Set action
        if (empty($_GET["action"])) {
        
            //Include basic info box
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderInfoBox.php');

           
            //Check if table not empty
            if ($logs->num_rows != 0) {

                //Add default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr><th scope="col">#</th><th scope="col">Name</th><th scope="col">Value</th><th scope="col">User-Key</th><th scope="col">Date</th><th scope="col">Client IP</th><th scope="col">X</th></tr></thead><tbody>';
                
                //print elements
                foreach ($logs as $data) {
                    
                    if ($data["status"] != "readed") {
                        if ($data["name"] == "Log reader" || $data["name"] == "Database" || $data["name"] == "Database delete" || $data["name"] == "Database list" || $data["name"] == "Database edit") {
                            echo "<tr class='text-primary'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong></strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Sended message" || $data["name"] == "Messages" || $data["name"] == "Todos") {
                            echo "<tr class='text-dark-yellow'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Paste") {
                            echo "<tr class='text-warning'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Uploader") {
                            echo "<tr class='text-success'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Login" || $data["name"] == "Logout" || $data["name"] == "Profile update" || $data["name"] == "Password update") {
                            echo "<tr class='text-red'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Encryptor") {
                            echo "<tr class='text-light-green'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } elseif ($data["name"] == "Success login") {
                            echo "<tr class='text-danger'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        } else {
                            echo "<tr><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["user_key"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$data["remote_addr"]."</strong><td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        }
                    }
                }
                
                //End of table
                echo '</tbody></table></div>';
            } else {
                echo"<h2 class=pageTitle>No relative logs were found</h2>";
            }

        } else {

            //Get action
            $action = $siteController->getCurrentAction();

            //If action = delete all
            if ($action == "deleteLogs") {

                //Include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LogDeleteConfirmationBox.php');

            //If action = set readed (Set all logs readed)
            } elseif ($action == "setReaded") {
            
                //Set all logs to readed
                $mysqlUtils->insertQuery("UPDATE logs SET status='readed' WHERE status='unreader'");
            
                //Redirect to log reader
                $urlUtils->jsRedirect("?admin=dashboard");

            } else {
                echo "<br><h2 class=pageTitle>Error action: $action not found!</h2>";
            }
        }

        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            //Check if page buttons can show
            if (($showLimit > $limitOnPage) or ($logs->num_rows == $limitOnPage)) {
                echo '<div class="pageButtonBox">';
            }
        
            //Print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }

            //Print next button if user on start page and can see next items
            if ($logs->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            //Check if page buttons can show
            if (($showLimit > $limitOnPage) or ($logs->num_rows == $limitOnPage)) {
                echo '</div><br>';
            }
        }        

        //Log action to mysql dsatabase 
        $mysqlUtils->logToMysql("Log reader", "User ".$adminController->getCurrentUsername()." showed logs");
    }
?>
</div>