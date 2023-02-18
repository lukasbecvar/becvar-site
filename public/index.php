<?php // main site index
	
	// init framework
	require_once("../framework/config/ConfigManager.php");
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

	// init controller system
	require_once("../framework/app/controller/ControllerManager.php");

	// include browser list for visitor controller
	require_once("../browser-list.php");

	// include services list for dashboard system
	require_once("../services-list.php");
	/////////////////////////////////////////////////////////////////////////////////////////////

	// init ConfigManager & Config objcts
	$pageConfig = new becwork\config\ConfigManager();

	// init HashUtils
	$hashUtils = new becwork\utils\HashUtils();

	// init CryptUtils
	$cryptUtils = new becwork\utils\CryptUtils();

	// init ResponseUtils
	$responseUtils = new becwork\utils\ResponseUtils();

	// init FileUtils
	$fileUtils = new becwork\utils\FileUtils();

	// init MainUtils
	$mainUtils = new becwork\utils\MainUtils();

	// init StringUtils
	$stringUtils = new becwork\utils\StringUtils();

	// init SessionUtils
	$sessionUtils = new becwork\utils\SessionUtils();

	// init UrlUtils
	$urlUtils = new becwork\utils\UrlUtils();

	// init CookieUtils
	$cookieUtils = new becwork\utils\CookieUtils();

	// init EscapeUtils
	$escapeUtils = new becwork\utils\EscapeUtils();

	// init MysqlUtils
	$mysqlUtils = new becwork\utils\MysqlUtils();

	// init BrowsersList
	$browsersList = new becwork\utils\BrowsersList();

	// init ServicesManager
	$servicesList = new becwork\services\ServicesManager();
	/////////////////////////////////////////////////////////////////////////////////////////////

	// autoload composer vendor
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		// redirect to error page if composer components is not installed
		if ($siteController->isSiteDevMode()) {
			die(include_once("../site/errors/VendorNotFound.php"));
		} else {
			die(include_once("../site/errors/Maintenance.php"));
		}
	} 
	
	// init detect mobile lib
	$mobileDetector = new Detection\MobileDetect;
	/////////////////////////////////////////////////////////////////////////////////////////////

	// set default encoding
	header('Content-type: text/html; charset='.$pageConfig->getValueByName('encoding'));

	// init whoops for error headling
	if ($siteController->isSiteDevMode()) {
		$whoops = new \Whoops\Run;
		$handlerer = new \Whoops\Handler\PrettyPageHandler();
		$whoops->pushHandler($handlerer);
		$whoops->register();
	}

	// check if page is in maintenance mode
	if($siteController->ifMaintenance()) {
		include_once("../site/errors/Maintenance.php");
	} else { 
		
		// init visitor system
		$visitorController->init();		

		
		// check if url-check is enabled
		if ($pageConfig->getValueByName("url-check")) { 

			// check if page loaded with valid url
			if (($siteController->getHTTPhost() != $pageConfig->getValueByName("url")) && ($siteController->getHTTPhost() != "www.".$pageConfig->getValueByName("url")) && $siteController->getHTTPhost() != "localhost") {
				$siteController->redirectError(400);
			}
		}

		// check if page running on https
		if ($pageConfig->getValueByName("https") == true && !$mainUtils->isSSL() && $siteController->getHTTPhost() != "localhost") {
			$siteController->redirectError(400);
		} 
				
		// include main page component or process
		else {
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// set logs disabler function by process
			if($siteController->getQueryString("process") == "disableLogsForMe") {
				include_once("../site/admin/LogsDisabler.php");
			}

			// set api manager process
			else if($siteController->getQueryString("process") == "api" or str_starts_with($siteController->getHTTPhost(), "api")) {
				include_once("../site/API.php");
			}
			
			// set image viewer by process
			else if($siteController->getQueryString("process") == "image") {
				include_once("../site/components/ImageViewer.php");
			}

			// set code paste page
			else if($siteController->getQueryString("process") == "paste") {

				// paste save 
				if ($siteController->getQueryString("method") == "save") {
					include_once("../site/components/paste/save.php");
				
				// paste view
				} else if (($siteController->getQueryString("method") != null) && $siteController->getQueryString("method") == "view") {
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
				if ($siteController->isCurrentPageAdmin()) {
					include_once("../site/admin/InitAdmin.php");
				} else {
					require_once("../site/Main.php");
				}
			}		
		}
	}
?>
