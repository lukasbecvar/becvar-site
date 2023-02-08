<div class="adminPanel">
<?php // log reader [admin component]

	// default values 
	$startByRow = 0;

	// check if user is owner
	if (!$adminController->isUserOwner()) {
		echo"<h2 class=pageTitle>Sorry you dont have permission to this page</h2>";
	} else {

		// page items limit
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
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/VisitorsManagerNavPanel.php');
        
        // get all visitors from table
        $visitors = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM visitors LIMIT $startByRow, $limitOnPage");

        if (empty($_GET["action"])) {

            // check if table not empty
            if ($visitors->num_rows != 0) {

                // default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr class="lineItem">
                    <th scope="col">#</th>
                    <th scope="col">Visited</th>
                    <th scope="col">First visit</th>
                    <th scope="col">Last visit</th>
                    <th scope="col">BrowserID</th>
                    <th scope="col">OS</th>
                    <th scope="col">Location</th>
                    <th scope="col">Banned</th>
                    <th scope="col">Client-IP</th>
                    <th scope="col">Ban</th>
                    <th scope="col">X</th>
                </tr></thead><tbody>';
                
                // print elements
                foreach ($visitors as $data) {

                    // get banned status
                    if ($visitorController->isVisitorBanned($data["ip_adress"])) {
                        $banStatus = "banned";
                    } else {
                        $banStatus = "unbanned";
                    }

                    // check if client ip not have > 15 characters
                    if (strlen($data["ip_adress"]) > 15) {
                        $formatedIP = substr($data["ip_adress"], 0, 15)."...";
                    } else {
                        $formatedIP = $data["ip_adress"];
                    }

                    // build link to ip log reader
                    $linkToIPLogs = "<a href='?admin=logReader&limit=50&startby=0&whereip=".$data["ip_adress"]."' class='log-reader-link'>".$formatedIP."</a>";

                    // if ip = session ip
                    if ($data["ip_adress"] == $adminController->getUserIPByToken($adminController->getUserToken())) {
                        // check if client ip not have > 15 characters
                        if (strlen($data["ip_adress"]) > 15) {
                            $linkToIPLogs = "<span class='text-warning'>".substr($data["ip_adress"], 0, 15)."...</span> [<span class='text-success'>You</span>]";

                        } else {
                            $linkToIPLogs = "<span class='text-warning'>".$data["ip_adress"]."</span> [<span class='text-success'>You</span>]";
                        }                
                    }

                    // shortify browserID
                    $data["browser"] = $visitorController->getShortBrowserID($data["browser"]);

                    // check if browser is Unknown
                    if ($data["browser"] == "Unknown") {
                        $data["browser"] = "<span class='text-red'>".$data["browser"]."</span>";
                    }

                    // check if first_visit & last_visit have same time
                    if ($data["first_visit"] == $data["last_visit"]) {
                        $data["first_visit"] = "<span class='text-red'>". $data["first_visit"]."</span>";
                        $data["last_visit"] = "<span class='text-red'>". $data["last_visit"]."</span>";
                    }

                    // check if location is CZ
                    if (str_starts_with(strtolower($data["location"]), 'cz')) {
                        $data["location"] = "<span class='text-warning'>".$data["location"]."</span>";
                    }

                    // check if banned
                    if ($banStatus == "banned") {
                        $banned = "<span class='text-red'>yes</span>";
                    } else {
                        $banned = "<span class='text-success'>no</span>";
                    }

                    // check if location is unknown
                    if ($data["location"] == "Unknown") {
                        $data["location"] = "<span class='text-red'>".$data["location"]."</span>";
                    }

                    // check if OS is unknown
                    if ($data["os"] == "Unknown OS") {
                        $data["os"] = "<span class='text-red'>".$data["os"]."</span>";
                    } else {
                        $data["os"] = "<span class='text-success'>".$data["os"]."</span>";
                    }

                    // check if visitor is banned
                    if ($banStatus == "banned") {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$data["id"]."&limit=500&startby=0'><strong>UNBAN</strong></a>";
                    } else {
                        $banLink = "<a class='deleteLinkTodos text-warning' href='?admin=visitors&action=ban&id=".$data["id"]."&limit=500&startby=0'><strong>BAN</strong></a>";
                    }

                    // build table row
                    $row = "<tr class='lineItem'>
                        <th scope='row'><strong>".$data["id"]."</strong>
                        <td><strong>".$data["visited_sites"]."</strong>
                        <td><strong>".$data["first_visit"]."</strong>
                        <td><strong>".$data["last_visit"]."</strong>
                        <td><strong>".$data["browser"]."</strong>
                        <td><strong>".$data["os"]."</strong>
                        <td><strong>".$data["location"]."</strong>
                        <td><strong>".$banned."</strong>
                        <td><strong>".$linkToIPLogs."</strong>
                        <td>".$banLink."
                        <td><a class='deleteLinkTodos' href='?admin=dbBrowser&delete=visitors&id=".$data["id"]."&visitors=yes'><strong>X</strong></a></td></td></th>
                    </tr>";

                    // print row to page
                    echo $row;
                }
                
                // end of table
                echo '</tbody></table></div>';
            } else {
                echo"<h2 class=pageTitle>No visitors were found</h2>";
            }
        } else {

            // get action
            $action = $siteController->getQueryString("action");

            // if action = delete all
            if ($action == "deleteVisitors") {

                // include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/VisitorsDeleteConfirmationBox.php');

            } elseif ($action == "ban") {

                // get id from query string
                $id = $mysqlUtils->escapeString($_GET["id"], true, true);

                // get visitor ip by id
                $ip = $visitorController->getVisitorIPByID($id);

                // check if user banned
                if ($visitorController->isVisitorBanned($ip)) {
                    
                    // log unban
                    $mysqlUtils->logToMysql("Unban visitor", "User ".$adminController->getCurrentUsername()." unbanned ip: ".$ip);

                    // unban user by ip
                    $visitorController->unbannVisitorByIP($ip);


                    // check if auto close seted
                    if (isset($_GET["close"])) {

                        // close tab
                        echo "<script>window.close();</script>";

                    } else {

                        // redirect to visitors
                        $urlUtils->jsRedirect("?admin=visitors&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
                    }
                } else {
                    
                    // check ban reason
                    if (!empty($_GET["reason"])) {

                        // escape ban reason
                        $reason = $mysqlUtils->escapeString($_GET["reason"], true, true);

                        // log ban
                        $mysqlUtils->logToMysql("Ban visitor", "User ".$adminController->getCurrentUsername()." banned ip: ".$ip);

                        // ban user by ip
                        $visitorController->bannVisitorByIP($ip, $reason);

                    } else {

                        // check if ban form submit
                        if (isset($_POST["submitBan"])) {

                            // check if ban reason seted
                            if (!empty($_POST["banReason"])) {
                                $banReason = $mysqlUtils->escapeString($_POST["banReason"], true, true);
                            } else {
                                $banReason = "no reason";
                            }

                            // check if auto close seted
                            if (isset($_GET["close"])) {

                                // redirect to banned with reason with autoclose
                                $urlUtils->jsRedirect("?admin=visitors&action=ban&id=".$_GET["id"]."&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0&reason=$banReason&close=yes");
                            } else {
                                // redirect to banned with reason
                                $urlUtils->jsRedirect("?admin=visitors&action=ban&id=".$_GET["id"]."&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0&reason=$banReason");
                            }
                        }

                        // include ban from
                        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/BanForm.php');
                    }
                }

                // check if auto close seted
                if (isset($_GET["close"])) {

                    // close tab
                    if ((!empty($_GET["reason"])) || (!empty($_POST["banReason"]))) {
                        echo "<script>window.close();</script>";
                    }

                } else {

                    // redirect to visitors
                    if ((!empty($_GET["reason"])) || (!empty($_POST["banReason"]))) {
                        $urlUtils->jsRedirect("?admin=visitors&limit=".$pageConfig->getValueByName("rowInTableLimit")."&startby=0");
                    }
                }

            } else {
                echo "<br><h2 class=pageTitle>Error action: $action not found!</h2>";
            }
        }

        // pager button box check
        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            // check if page buttons can show
            if (($showLimit > $limitOnPage) or ($visitors->num_rows == $limitOnPage)) {
                echo '<div class="pageButtonBox">';
            }
        
            // print back button if user in next page
            if ($showLimit > $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=visitors&limit='.$nextLimitBack.'&startby='.$nextStartByRowBack.'>Back</a><br>';
            }

            // print next button if user on start page and can see next items
            if ($visitors->num_rows == $limitOnPage) {
                echo '<br><a class="backPageButton" href=?admin=visitors&limit='.$nextLimit.'&startby='.$nextStartByRow.'>Next</a><br>';	
            }
    
            // check if page buttons can show
            if (($showLimit > $limitOnPage) or ($visitors->num_rows == $limitOnPage)) {
                echo '</div><br>';
            }
        }        

        // log action to mysql database 
        $mysqlUtils->logToMysql("Log reader", "User ".$adminController->getCurrentUsername()." showed visitors");
    }
?>
</div>