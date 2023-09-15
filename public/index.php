<?php // main site index
	
	// init framework
	require_once("../app/config/ConfigUtils.php");
	require_once("../app/encryption/HashUtils.php");
	require_once("../app/encryption/CryptUtils.php");
	require_once("../app/utils/ResponseUtils.php");
	require_once("../app/utils/JsonUtils.php");
	require_once("../app/utils/MainUtils.php");
	require_once("../app/utils/StringUtils.php");
	require_once("../app/utils/SessionUtils.php");
	require_once("../app/utils/UrlUtils.php");
	require_once("../app/utils/CookieUtils.php");
	require_once("../app/utils/EscapeUtils.php");
	require_once("../app/mysql/MysqlUtils.php");

	// init manager system
	require_once("../app/manager/ManagerList.php");

	// include browser list for visitors manager
	require_once("../browser-list.php");

	// include services list for dashboard system
	require_once("../services-list.php");
	/////////////////////////////////////////////////////////////////////////////////////////////

	// init objects
	$config = new becwork\config\ConfigUtils();
	$hash_utils = new becwork\utils\HashUtils();
	$crypt_utils = new becwork\utils\CryptUtils();
	$response_utils = new becwork\utils\ResponseUtils();
	$json_utils = new becwork\utils\JsonUtils();
	$main_utils = new becwork\utils\MainUtils();
	$string_utils = new becwork\utils\StringUtils();
	$session_utils = new becwork\utils\SessionUtils();
	$url_utils = new becwork\utils\UrlUtils();
	$cookie_utils = new becwork\utils\CookieUtils();
	$escape_utils = new becwork\utils\EscapeUtils();
	
	// visitors manager
	$browsers_list = new becwork\utils\BrowsersList();
	$services_list = new becwork\services\ServicesManager();

	// database config
	$db_ip = $config->get_value("database-host");
	$db_name = $config->get_value("database-name");
	$db_username = $config->get_value("database-username");
	$db_password = $config->get_value("database-password");
	
	// init mysql controller
	$mysql = new becwork\utils\MysqlUtils($db_ip, $db_name, $db_username, $db_password);
	/////////////////////////////////////////////////////////////////////////////////////////////

	// check if composer installed
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		// handle error redirect to error page if composer components is not installed
		$site_manager->handle_error("vendor directory not found, plese run composer install", 520);
	} 
	
	// init detect mobile lib
	$mobileDetector = new Detection\MobileDetect;
	/////////////////////////////////////////////////////////////////////////////////////////////

	// set default encoding
	header('Content-type: text/html; charset='.$config->get_value('encoding'));

	// init whoops for error headling
	if ($site_manager->is_dev_mode()) {
		$whoops = new \Whoops\Run;
		$handlerer = new \Whoops\Handler\PrettyPageHandler();
		$whoops->pushHandler($handlerer);
		$whoops->register();
	}

	// check if page is in maintenance mode
	if($site_manager->is_maintenance()) {
		include_once("../site/errors/Maintenance.php");
	} else { 
		
		// init visitor system
		$visitor_manager->init();		
		
		// check if url-check is enabled
		if ($config->get_value("url-check")) {

			// check if page loaded with valid url 
			if (!$site_manager->is_valid_url()) {

				// handle invalid url error
				$site_manager->handle_error("invalid url load, plese check [url-check, url]: values in config.php", 400);
			}
		}

		// check if page running on https
		if (!$site_manager->check_ssl()) {

			// handle invalid https error
			$site_manager->handle_error("this site can run only on https protocol, check your url in browser or config.php", 400);
		} 
				
		// include main page component or process
		else {
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// set main page component in process is empty
			else {

				// check if page is admin or normal site
				if ($site_manager->is_admin_site()) {
					include_once("../site/admin/InitAdmin.php");
				} else {
					require_once("../site/public/Main.php");
				}
			}		
		}
	}
?>