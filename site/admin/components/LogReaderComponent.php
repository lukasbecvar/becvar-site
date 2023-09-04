<div class="adminPanel">
<?php // log reader [admin component]

	// default values 
	$startByRow = 0;

	// check if user is owner
	if (!$userManager->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		// items limit
		$limitOnPage = $config->getValue("rowInTableLimit");

		// if limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			// get show limit form url
			$showLimit = $escapeUtils->specialCharshStrip($_GET["limit"]);

			// get start row form url
			$startByRow = $escapeUtils->specialCharshStrip($_GET["startby"]);

			// set next limit
			$nextLimit = (int) $showLimit + $limitOnPage;

			// set next start by for pages
			$nextStartByRow = (int) $startByRow + $limitOnPage;
			$nextLimitBack = (int) $showLimit - $limitOnPage;
			$nextStartByRowBack = (int) $startByRow - $limitOnPage;	
		}

        // include navbar
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderNavPanel.php');
        
        // get where ip string
        $whereIP = $siteManager->getQueryString("whereip");

        // check if where ip select
        if ($whereIP == null) {
            
            // get all logs from table
            $logs = $mysql->fetch("SELECT * FROM logs WHERE status NOT LIKE 'readed' ORDER BY id DESC LIMIT $startByRow, $limitOnPage");          
        } else {
            
            // select logs where ip
            $logs = $mysql->fetch("SELECT * FROM logs WHERE remote_addr = '$whereIP' LIMIT $startByRow, $limitOnPage");
        }

        // set action
        if (empty($_GET["action"])) {
        
            // include basic info box
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/LogReaderInfoBox.php');

           
            // check if table not empty
            if (count($logs) != 0) {

                // default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr class="lineItem">
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
                    $userID = $visitorManager->getVisitorIDByIP($data["remote_addr"]);

                    // ban link builder
                    if ($visitorManager->isVisitorBanned($data["remote_addr"])) {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=yes' target='blank_'>UNBAN</a>";
                    } else {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$userID."&limit=500&startby=0&close=yes' target='blank_'>BAN</a>";
                    }
                    
                    // build link to ip (show logs where ip)
                    $linkToIP = "?admin=logReader&limit=".$config->getValue("rowInTableLimit")."&startby=0&whereip=".$data["remote_addr"];

                    // get location string from visitors database
                    $location = $visitorManager->getVisitorLocationFromDatabase($userID);

                    // table row builder
                    if (($data["status"] != "readed") || ($data["status"] == "readed" && $whereIP != null)) {
                        
                        // blue logs
                        if ($data["name"] == "project-update" || $data["name"] == "log-reader" || $data["name"] == "database") {
                            $row = "<tr class='lineItem text-primary'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong></strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-primary'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // yellow logs
                        } elseif ($data["name"] == "emergency-shutdown" || $data["name"] == "recived-message" || $data["name"] == "close-message" || $data["name"] == "todo-manager") {
                            $row = "<tr class='lineItem text-dark-yellow'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-dark-yellow'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // dark yellow logs
                        } elseif ($data["name"] == "config-update" || $data["name"] == "paste" || $data["name"] == "banned" || $data["name"] == "message-block" || $data["name"] == "unban-visitor") {
                            $row = "<tr class='lineItem text-warning'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-warning'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // green logs
                        } elseif ($data["name"] == "image-uploader" || $data["name"] == "image-load") {
                            $row = "<tr class='lineItem text-success'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-success'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // red logs
                        } elseif ($data["name"] == "authenticator" || $data["name"] == "profile-update" || $data["name"] == "geolocate-error") {
                            $row = "<tr class='lineItem text-red'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-red'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // dark red logs
                        } elseif ($data["name"] == "ban-visitor") {
                            $row = "<tr class='lineItem text-danger'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link text-danger'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
                        
                        // others
                        } else {
                            $row = "<tr class='lineItem'><th scope='row'><strong>".$data["id"]."</strong><td><strong>".$data["name"]."</strong><td><strong>".$data["value"]."</strong><td><strong>".$data["date"]."</strong><td><strong>".$visitorManager->getShortBrowserID($data["browser"])."</strong><td><strong><a href='".$linkToIP."' class='log-reader-link color-white'>".$data["remote_addr"]."</a></strong><td><strong>".$location."</strong><td>".$banLink."<td><a class='deleteLinkTodos' href='".'?admin=dbBrowser&delete=logs&id='.$data["id"]."&reader=yes'><strong>X</strong></a></td></td></th></tr>";
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
            $action = $siteManager->getQueryString("action");

            // action = delete all
            if ($action == "deleteLogs") {

                // include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LogDeleteConfirmationBox.php');

            // action = set readed (Set all logs readed)
            } elseif ($action == "setReaded") {
            
                // set all logs to readed
                $mysql->insertQuery("UPDATE logs SET status='readed' WHERE status='unreaded'");
            
                // redirect to log reader
                $urlUtils->jsRedirect("?admin=dashboard");

            } else {
                echo "<br><h2 class=pageTitle>Error action: $action not found!</h2>";
            }
        }

        // pager button box check
        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            // check if page buttons can show
            if (($showLimit > $limitOnPage) or (count($logs) == $limitOnPage)) {
                echo '<div class="pageButtonBox">';
            }
        
            // print back button if user in next page
            if ($showLimit > $limitOnPage) {
                
                // check if where ip is null
                if ($whereIP == null) {

                    // normal back button
                    echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
                } else {

                    // back button with where ip
                    echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'&whereip='.$whereIP.'>Back</a><br>';
                }
            }

            // print next button if user on start page and can see next items
            if (count($logs) == $limitOnPage) {

                // check if where ip is null
                if ($whereIP == null) {
                    
                    // normal next button
                    echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
                } else {

                    // next button with where ip
                    echo '<br><a class="backPageButton" href=?admin=logReader&limit='.$nextLimit.'&startby='.$nextStartByRow.'&whereip='.$whereIP.'>Next</a><br>';	
                }
            }
    
            // check if page buttons can show
            if (($showLimit > $limitOnPage) or (count($logs) == $limitOnPage)) {
                echo '</div><br>';
            }
        }        

        // log action to mysql database 
        $mysql->logToMysql("log-reader", "user: ".$userManager->getCurrentUsername()." showed logs");
    }
?>
</div>