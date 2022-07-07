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
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/VisitorsManagerNavPanel.php');
        
        //Get all visitors from table
        $visitors = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM visitors LIMIT $startByRow, $limitOnPage");

        if (empty($_GET["action"])) {

            //Check if table not empty
            if ($visitors->num_rows != 0) {

                //Add default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr><th scope="col">#</th><th scope="col">Visited</th><th scope="col">First visit</th><th scope="col">Last visit</th><th scope="col">BrowserID</th><th scope="col">Location</th><th scope="col">Banned</th><th scope="col">Client-IP</th><th scope="col">Ban</th><th scope="col">X</th></tr></thead><tbody>';
                
                //print elements
                foreach ($visitors as $data) {

                    //If ip = session ip
                    if ($data["ip_adress"] == $mainUtils->getRemoteAdress()) {
                        $data["ip_adress"] = "<span class='text-warning'>".$data["ip_adress"]."</span>";
                    }

                    //Check if browser not have > 32 characters
                    if (strlen($data["browser"]) > 32) {
                        $data["browser"] = substr($data["browser"], 0, 32)."...";
                    }
                    
                    //Check if browser is undefined
                    if ($data["browser"] == "Undefined") {
                        $data["browser"] = "<span class='text-red'>".$data["browser"]."</span>";
                    }

                    //Check if first_visit & last_visit have same time
                    if ($data["first_visit"] == $data["last_visit"]) {
                        $data["first_visit"] = "<span class='text-red'>". $data["first_visit"]."</span>";
                        $data["last_visit"] = "<span class='text-red'>". $data["last_visit"]."</span>";
                    }

                    //Check if location is CZ
                    if (str_starts_with($data["location"], 'CZ/') or str_starts_with($data["location"], 'cz/')) {
                        $data["location"] = "<span class='text-warning'>".$data["location"]."</span>";
                    }

                    //Check if banned
                    if ($data["banned"] == "yes") {
                        $data["banned"] = "<span class='text-red'>".$data["banned"]."</span>";
                    } else {
                        $data["banned"] = "<span class='text-success'>".$data["banned"]."</span>";
                    }
                    
                    //Build table row
                    $row = "<tr class='lineItem'>
                        <th scope='row'><strong>".$data["id"]."</strong>
                        <td><strong>".$data["visited_sites"]."</strong>
                        <td><strong>".$data["first_visit"]."</strong>
                        <td><strong>".$data["last_visit"]."</strong>
                        <td><strong>".$data["browser"]."</strong>
                        <td><strong>".$data["location"]."</strong>
                        <td><strong>".$data["banned"]."</strong>
                        <td><strong>".$data["ip_adress"]."</strong>
                        <td><a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$data["id"]."&limit=500&startby=0'><strong>Ban/u</strong></a>
                        <td><a class='deleteLinkTodos' href='?admin=dbBrowser&delete=visitors&id=".$data["id"]."&visitors=yes'><strong>X</strong></a></td></td></th>
                    </tr>";

                    //Print row to page
                    echo $row;
                }
                
                //End of table
                echo '</tbody></table></div>';
            } else {
                echo"<h2 class=pageTitle>No visitors were found</h2>";
            }
        } else {

            //Get action
            $action = $siteController->getCurrentAction();

            //If action = delete all
            if ($action == "deleteVisitors") {

                //Include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/VisitorsDeleteConfirmationBox.php');

            } elseif ($action == "ban") {

                //Get id from query string
                $id = $mysqlUtils->escapeString($_GET["id"], true, true);

                //Get visitor ip by id
                $ip = $visitorController->getVisitorIPByID($id);

                //Check if user banned
                if ($visitorController->isVisitorBanned($ip)) {
                    //Unban user by ip
                    $visitorController->unbannVisitorByIP($ip);

                    //Log unban
                    $mysqlUtils->logToMysql("Unban visitor", "User ".$adminController->getCurrentUsername()." unbanned ip: ".$ip);

                } else {
                    //Ban user by ip
                    $visitorController->bannVisitorByIP($ip);

                    //Log ban
                    $mysqlUtils->logToMysql("Ban visitor", "User ".$adminController->getCurrentUsername()." banned ip: ".$ip);
                }

                //Redirect to visitors
                $urlUtils->jsRedirect("?admin=visitors&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");

            } else {
                echo "<br><h2 class=pageTitle>Error action: $action not found!</h2>";
            }

        }


        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            //Check if page buttons can show
            if (($showLimit > $limitOnPage) or ($visitors->num_rows == $limitOnPage)) {
                echo '<div class="pageButtonBox">';
            }
        
            //Print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=visitors&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }

            //Print next button if user on start page and can see next items
            if ($visitors->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=visitors&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            //Check if page buttons can show
            if (($showLimit > $limitOnPage) or ($visitors->num_rows == $limitOnPage)) {
                echo '</div><br>';
            }
        }        

        //Log action to mysql database 
        $mysqlUtils->logToMysql("Log reader", "User ".$adminController->getCurrentUsername()." showed visitors");
    }
?>
</div>