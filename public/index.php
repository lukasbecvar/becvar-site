<?php // main site index
	
	// init framework
	require_once("../framework/config/ConfigUtils.php");
	require_once("../framework/crypt/HashUtils.php");
	require_once("../framework/crypt/CryptUtils.php");
	require_once("../framework/utils/ResponseUtils.php");
	require_once("../framework/utils/FileUtils.php");
	require_once("../framework/utils/MainUtils.php");
	require_once("../framework/utils/StringUtils.php");
	require_once("../framework/utils/SessionUtils.php");
	require_once("../framework/utils/UrlUtils.php");
	require_once("../framework/utils/CookieUtils.php");
	require_once("../framework/utils/EscapeUtils.php");
	require_once("../framework/mysql/MysqlUtils.php");

	// init manager system
	require_once("../framework/app/manager/ManagerList.php");

	// include browser list for visitors manager
	require_once("../browser-list.php");

	// include services list for dashboard system
	require_once("../services-list.php");
	/////////////////////////////////////////////////////////////////////////////////////////////

	// init objects
	$config = new becwork\config\ConfigUtils();
	$hashUtils = new becwork\utils\HashUtils();
	$cryptUtils = new becwork\utils\CryptUtils();
	$responseUtils = new becwork\utils\ResponseUtils();
	$fileUtils = new becwork\utils\FileUtils();
	$mainUtils = new becwork\utils\MainUtils();
	$stringUtils = new becwork\utils\StringUtils();
	$sessionUtils = new becwork\utils\SessionUtils();
	$urlUtils = new becwork\utils\UrlUtils();
	$cookieUtils = new becwork\utils\CookieUtils();
	$escapeUtils = new becwork\utils\EscapeUtils();
	
	// visitors manager
	$browsersList = new becwork\utils\BrowsersList();
	$servicesList = new becwork\services\ServicesManager();

	// database config
	$db_ip = $config->getValue("mysql-address");
	$db_name = $config->getValue("mysql-database");
	$db_username = $config->getValue("mysql-username");
	$db_password = $config->getValue("mysql-password");
	
	// init mysql controller
	$mysql = new becwork\utils\MysqlUtils($db_ip, $db_name, $db_username, $db_password);
	/////////////////////////////////////////////////////////////////////////////////////////////

	// autoload composer vendor
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		// redirect to error page if composer components is not installed
		if ($siteManager->isSiteDevMode()) {
			die(include_once("../site/errors/VendorNotFound.php"));
		} else {
			die(include_once("../site/errors/Maintenance.php"));
		}
	} 
	
	// init detect mobile lib
	$mobileDetector = new Detection\MobileDetect;
	/////////////////////////////////////////////////////////////////////////////////////////////

	// set default encoding
	header('Content-type: text/html; charset='.$config->getValue('encoding'));

	// init whoops for error headling
	if ($siteManager->isSiteDevMode()) {
		$whoops = new \Whoops\Run;
		$handlerer = new \Whoops\Handler\PrettyPageHandler();
		$whoops->pushHandler($handlerer);
		$whoops->register();
	}

	// check if page is in maintenance mode
	if($siteManager->ifMaintenance()) {
		include_once("../site/errors/Maintenance.php");
	} else { 
		
		// init visitor system
		$visitorManager->init();		
		
		// check if url-check is enabled
		if ($config->getValue("url-check")) { 

			// check if page loaded with valid url
			if (($siteManager->getHTTPhost() != $config->getValue("url")) && ($siteManager->getHTTPhost() != "www.".$config->getValue("url")) && $siteManager->getHTTPhost() != "localhost") {
				$siteManager->redirectError(400);
			}
		}

		// check if page running on https
		if ($config->getValue("https") == true && !$mainUtils->isSSL() && $siteManager->getHTTPhost() != "localhost") {
			$siteManager->redirectError(400);
		} 
				
		// include main page component or process
		else {
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// set logs disabler function by process
			if($siteManager->getQueryString("process") == "disableLogsForMe") {
				include_once("../site/admin/LogsDisabler.php");
			}
			
			// set image viewer by process
			else if($siteManager->getQueryString("process") == "image") {
				include_once("../site/components/ImageViewer.php");
			}

			// set code paste page
			else if($siteManager->getQueryString("process") == "paste") {

				// paste save 
				if ($siteManager->getQueryString("method") == "save") {
					include_once("../site/components/paste/save.php");
				
				// paste view
				} else if (($siteManager->getQueryString("method") != null) && $siteManager->getQueryString("method") == "view") {
					include_once("../site/components/paste/view.php");
				
				// paste init
				} else {
					include_once("../site/components/paste/index.php");
				}	
			}
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// set main page component in process is empty
			else {

				// check if page is admin or normal site
				if ($siteManager->isCurrentPageAdmin()) {
					include_once("../site/admin/InitAdmin.php");
				} else {
					require_once("../site/Main.php");
				}
			}		
		}
	}
?>
