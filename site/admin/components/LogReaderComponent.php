<div class="adminPanel">
<?php // log reader [admin component]

	// default values 
	$startByRow = 0;

	// check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		// items limit
		$limitOnPage = $pageConfig->getValueByName("rowInTableLimit");

		// if limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			// get show limit form url
			$showLimit = $mysqlUtils->escapeString($_GET["limit"], true, true);

			// get start row form url
			$startByRow = $mysqlUtils->escapeString($_GET["startby"], true, true);

			// set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			// set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}

        // include navbar
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderNavPanel.php');
        
        // get all logs from table
        $logs = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM logs WHERE status NOT LIKE 'readed' ORDER BY id DESC LIMIT $startByRow, $limitOnPage");


        // set action
        if (empty($_GET["action"])) {
        
            // include basic info box
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderInfoBox.php');

           
            // check if table not empty
            if ($logs->num_rows != 0) {

                // default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Value</th>
                    <th scope="col">Date</th>
                    <th scope="col">Browser</th>
                    <th scope="col">Client IP</th>
                    <th scope="col">Location</th>
                    <th scope="col">BAN</th>
                    <th scope="col">X</th>
                </tr></thead><tbody>';
                
                // print elements
                foreach ($logs as $data) {
                    
                    // get visitor id
                    $userID = $visitorController->getVisitorIDByIP($data["remote_addr"]);

                    // ban link builder
                    if ($visitorController->isVisitorBanned($data["remote_addr"])) {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=yes' target='blank_'>UNBAN</a>";
                    } else {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=yes' target='blank_'>BAN</a>";
                    }
                    
                    // get location string from visitors database
                    $location = $visitorController->getVisitorLocationFromDatabase($userID);

                    // table row builder
                    if ($data["status"] != "readed") {
                        
                        // database logs
                        if ($data["name"] == "Log reader" || $data["name"] == "Database" || $data["name"] == "Database delete" || $data["name"] == "Database insert" || $data["name"] == "Database list" || $data["name"] == "Database edit") {
                            $row = "<tr class='text-primary'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong></strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // contact & todos logs
                        } elseif ($data["name"] == "Sended message" || $data["name"] == "Messages" || $data["name"] == "Todos") {
                            $row = "<tr class='text-dark-yellow'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // paste logs
                        } elseif ($data["name"] == "Paste" || $data["name"] == "Banned" || $data["name"] == "Unban visitor") {
                            $row = "<tr class='text-warning'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // upload logs
                        } elseif ($data["name"] == "Uploader" || $data["name"] == "Image-load") {
                            $row = "<tr class='text-success'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // login, logout, password logs
                        } elseif ($data["name"] == "Login" || $data["name"] == "Logout" || $data["name"] == "Profile update" || $data["name"] == "Password update") {
                            $row = "<tr class='text-red'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // success login logs
                        } elseif ($data["name"] == "Success login" || $data["name"] == "Ban visitor") {
                            $row = "<tr class='text-danger'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // others
                        } else {
                            $row = "<tr><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorController->getShortBrowserID($data["browser"])."</strong><td><strong>".$data["remote_addr"]."</strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        }

                        // prit row to table
                        echo $row;
                    }
                }
                
                // table end
                echo '</tbody></table></div>';
            } else {
                echo"<h2 class=pageTitle>No relative logs were found</h2>";
            }

        } else {

            // get action
            $action = $siteController->getQueryString("action");

            // action = delete all
            if ($action == "deleteLogs") {

                // include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LogDeleteConfirmationBox.php');

            // action = set readed (Set all logs readed)
            } elseif ($action == "setReaded") {
            
                // set all logs to readed
                $mysqlUtils->insertQuery("UPDATE logs SET status='readed' WHERE status='unreaded'");
            
                // redirect to log reader
                $urlUtils->jsRedirect("?admin=dashboard");

            } else {
                echo "<br><h2 class=pageTitle>Error action: $action not found!</h2>";
            }
        }

        // pager button box check
        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            // check if page buttons can show
            if (($showLimit > $limitOnPage) or ($logs->num_rows == $limitOnPage)) {
                echo '<div class="pageButtonBox">';
            }
        
            // print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }

            // print next button if user on start page and can see next items
            if ($logs->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            // check if page buttons can show
            if (($showLimit > $limitOnPage) or ($logs->num_rows == $limitOnPage)) {
                echo '</div><br>';
            }
        }        

        // log action to mysql database 
        $mysqlUtils->logToMysql("Log reader", "User ".$adminController->getCurrentUsername()." showed logs");
    }
?>
</div>