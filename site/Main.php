<?php // main site (redirect to valid site component)

	// check if page is in maintenance mode
	if($site_manager->is_maintenance()) {
		$url_utils->js_redirect("error.php?code=maintenance");
	} else { 	
		
        // connect to database
        $mysql->connect();

		// init visitor system
		$visitor_manager->init();

		// set logs disabler function by process
		if($site_manager->get_query_string("process") == "disableLogsForMe") {
			include_once("../site/admin/LogsDisabler.php");
		}
			
		// set image viewer by process
		else if($site_manager->get_query_string("process") == "image") {
			include_once("../site/public/components/ImageViewer.php");
		}

		// set code paste page
		else if($site_manager->get_query_string("process") == "paste") {

			// paste save 
			if ($site_manager->get_query_string("method") == "save") {
				include_once("../site/paste/save.php");
				
			// paste view
			} else if ($site_manager->get_query_string("method") == "view") {
				include_once("../site/paste/view.php");
				
			// paste init
			} else {
				include_once("../site/paste/index.php");
			}	
		}

		// set main page component if process is empty
		else {

			// check if page is admin or normal site
			if ($site_manager->is_admin_site()) {
				include_once("../site/admin/InitAdmin.php");
			} else {
				require_once("../site/public/InitPublic.php");
			}
		}		
	}
