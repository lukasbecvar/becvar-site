<div class="admin-panel">
<?php // log reader [admin component]

	// default values 
	$start_by_row = 0;

	// check if user is owner
	if (!$user_manager->is_user_Owner()) {
		echo"<h2 class=page-title>Sorry you dont have permission to this page</h2>";
	} else {

		// page items limit
		$limit_on_page = $config->get_value("row-in-table-limit");

		// if limit get seted make this trash part of code xD
		if (isset($_GET["limit"]) && isset($_GET["startby"])) {

			// get show limit form url
			$show_limit = $escape_utils->special_chars_strip($_GET["limit"]);

			// get start row form url
			$start_by_row = $escape_utils->special_chars_strip($_GET["startby"]);

			// set next limit
			$next_limit = (int) $show_limit + $limit_on_page;

			// set next start by for pages
			$next_start_by_row = (int) $start_by_row + $limit_on_page;
			$next_limit_back = (int) $show_limit - $limit_on_page;
			$next_start_by_row_back = (int) $start_by_row - $limit_on_page;	
		}

        // include navbar
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/VisitorsManagerNavPanel.php');
        
        // get all visitors from table
        $visitors = $mysql->fetch("SELECT * FROM visitors LIMIT $start_by_row, $limit_on_page");

        if (empty($_GET["action"])) {

            // check if table not empty
            if (count($visitors) != 0) {

                // default table structure
                echo '<div class="table-responsive"><table class="table table-dark"><thead><tr class="line-item">
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
                    if ($visitor_manager->is_visitor_banned($data["ip_adress"])) {
                        $ban_status = "banned";
                    } else {
                        $ban_status = "unbanned";
                    }

                    // check if client ip not have > 15 characters
                    if (strlen($data["ip_adress"]) > 15) {
                        $formated_ip = substr($data["ip_adress"], 0, 15)."...";
                    } else {
                        $formated_ip = $data["ip_adress"];
                    }

                    // build link to ip log reader
                    $link_to_ip_logs = "<a href='?admin=logReader&limit=50&startby=0&whereip=".$data["ip_adress"]."' class='log-reader-link'>".$formated_ip."</a>";

                    // if ip = session ip
                    if ($data["ip_adress"] == $user_manager->get_user_ip_by_token($user_manager->get_token())) {
                        // check if client ip not have > 15 characters
                        if (strlen($data["ip_adress"]) > 15) {
                            $link_to_ip_logs = "<span class='text-warning'>".substr($data["ip_adress"], 0, 15)."...</span> [<span class='text-success'>You</span>]";

                        } else {
                            $link_to_ip_logs = "<span class='text-warning'>".$data["ip_adress"]."</span> [<span class='text-success'>You</span>]";
                        }                
                    }

                    // shortify browserID
                    $data["browser"] = $visitor_manager->get_short_browser_id($data["browser"]);

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
                    if ($ban_status == "banned") {
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
                    if ($ban_status == "banned") {
                        $ban_link = "<a class='delete-link-todos text-warning' href='?admin=visitors&action=ban&id=".$data["id"]."&limit=500&startby=0'><strong>UNBAN</strong></a>";
                    } else {
                        $ban_link = "<a class='delete-link-todos text-warning' href='?admin=visitors&action=ban&id=".$data["id"]."&limit=500&startby=0'><strong>BAN</strong></a>";
                    }

                    // build table row
                    $row = "<tr class='line-item'>
                        <th scope='row'><strong>".$data["id"]."</strong>
                        <td><strong>".$data["visited_sites"]."</strong>
                        <td><strong>".$data["first_visit"]."</strong>
                        <td><strong>".$data["last_visit"]."</strong>
                        <td><strong>".$data["browser"]."</strong>
                        <td><strong>".$data["os"]."</strong>
                        <td><strong>".$data["location"]."</strong>
                        <td><strong>".$banned."</strong>
                        <td><strong>".$link_to_ip_logs."</strong>
                        <td>".$ban_link."
                        <td><a class='delete-link-todos' href='?admin=dbBrowser&delete=visitors&id=".$data["id"]."&visitors=yes'><strong>X</strong></a></td></td></th>
                    </tr>";

                    // print row to page
                    echo $row;
                }
                
                // end of table
                echo '</tbody></table></div>';
            } else {
                echo"<h2 class=page-title>No visitors were found</h2>";
            }
        } else {

            // get action
            $action = $site_manager->get_query_string("action");

            // if action = delete all
            if ($action == "deleteVisitors") {

                // include conf box
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/VisitorsDeleteConfirmationBox.php');

            } elseif ($action == "ban") {

                // get id from query string
                $id = $escape_utils->special_chars_strip($_GET["id"]);

                // get visitor ip by id
                $ip = $visitor_manager->get_visitor_ip_by_id($id);

                // check if user banned
                if ($visitor_manager->is_visitor_banned($ip)) {
                    
                    // log unban
                    $mysql->log("unban-visitor", "user ".$user_manager->get_username()." unbanned ip: ".$ip);

                    // unban user by ip
                    $visitor_manager->unban_visitor_by_ip($ip);

                    // check if auto close seted
                    if (isset($_GET["close"])) {

                        // close tab
                        echo "<script>window.close();</script>";

                    } else {

                        // redirect to visitors
                        $url_utils->js_redirect("?admin=visitors&limit=".$config->get_value("row-in-table-limit")."&startby=0");
                    }
                } else {
                    
                    // check ban reason
                    if (!empty($_GET["reason"])) {

                        // escape ban reason
                        $reason = $escape_utils->special_chars_strip($_GET["reason"]);

                        // log ban
                        $mysql->log("ban-visitor", "user ".$user_manager->get_username()." banned ip: ".$ip);

                        // ban user by ip
                        $visitor_manager->ban_visitor_by_ip($ip, $reason);

                        // check if ban email require
                        if (!empty($_GET["email"])) {
                            
                            // get email form query string
                            $email = $site_manager->get_query_string("email");
                            
                            // block email
                            $contact_manager->block_email($email);
                        }

                    } else {

                        // check if ban form submit
                        if (isset($_POST["submitBan"])) {

                            // check if ban reason seted
                            if (!empty($_POST["banReason"])) {
                                $ban_reason = $escape_utils->special_chars_strip($_POST["banReason"]);
                            } else {
                                $ban_reason = "no reason";
                            }

                            // check if auto close seted
                            if (isset($_GET["close"])) {

                                // redirect to banned with reason with autoclose
                                $url_utils->js_redirect("?admin=visitors&action=ban&id=".$_GET["id"]."&limit=".$config->get_value("row-in-table-limit")."&startby=0&reason=$ban_reason&close=yes");
                            } else {
                                // redirect to banned with reason
                                $url_utils->js_redirect("?admin=visitors&action=ban&id=".$_GET["id"]."&limit=".$config->get_value("row-in-table-limit")."&startby=0&reason=$ban_reason");
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
                        $url_utils->js_redirect("?admin=visitors&limit=".$config->get_value("row-in-table-limit")."&startby=0");
                    }
                }

            } else {
                echo "<br><h2 class=page-title>Error action: $action not found!</h2>";
            }
        }

        // pager button box check
        if (isset($_GET["limit"]) and isset($_GET["startby"]) and !isset($_GET["action"])) {

            // check if page buttons can show
            if (($show_limit > $limit_on_page) or (count($visitors) == $limit_on_page)) {
                echo '<div class="page-button-box">';
            }
        
            // print back button if user in next page
            if ($show_limit > $limit_on_page) {
                echo '<br><a class="back-page-button" href=?admin=visitors&limit='.$next_limit_back.'&startby='.$next_start_by_row_back.'>Back</a><br>';
            }

            // print next button if user on start page and can see next items
            if (count($visitors) == $limit_on_page) {
                echo '<br><a class="back-page-button" href=?admin=visitors&limit='.$next_limit.'&startby='.$next_start_by_row.'>Next</a><br>';	
            }
    
            // check if page buttons can show
            if (($show_limit > $limit_on_page) or (count($visitors) == $limit_on_page)) {
                echo '</div><br>';
            }
        }        

        // log action to mysql database 
        $mysql->log("log-reader", "user ".$user_manager->get_username()." showed visitors");
    }
?>
</div>