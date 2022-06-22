<?php //Main page file index

	//Init framework
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

	//Init application controllers
	require_once("../framework/app/controller/ControllersManager.php");

	/////////////////////////////////////////////////////////////////////////////////////////////

	//Init ConfigManager
	$pageConfig = new ConfigManager();

	//Init HashUtils
	$hashUtils = new HashUtils();

	//Init CryptUtils
	$cryptUtils = new CryptUtils();

	//Init ResponseUtils
	$responseUtils = new ResponseUtils();

	//Init FileUtils
	$fileUtils = new FileUtils();

	//Init MainUtils
	$mainUtils = new MainUtils();

	//Init StringUtils
	$stringUtils = new StringUtils();

	//Init SessionUtils
	$sessionUtils = new SessionUtils();

	//Init UrlUtils
	$urlUtils = new UrlUtils();

	//Init CookieUtils
	$cookieUtils = new CookieUtils();

	//Init EscapeUtils
	$escapeUtils = new EscapeUtils();

	//Init MysqlUtils
	$mysqlUtils = new MysqlUtils();

	/////////////////////////////////////////////////////////////////////////////////////////////

	//Autoload composer vendor
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		//Redirect to error page if composer components is not installed
		if ($pageConfig->getValueByName("dev_mode") == true) {
			die(include_once("../site/errors/VendorNotFound.php"));
		} else {
			die(include_once("../site/errors/Maintenance.php"));
		}
	} 
	
	//Init detect mobile lib
	$mobileDetector = new Mobile_Detect;
	/////////////////////////////////////////////////////////////////////////////////////////////


	//Set default encoding
	header('Content-type: text/html; charset='.$pageConfig->getValueByName('encoding'));

	//Init whoops for error headling
	if ($pageConfig->getValueByName("dev_mode") == true) {
		if ($_SERVER['HTTP_HOST'] == "localhost") {
			$whoops = new \Whoops\Run;
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			$whoops->register();		  
		}
	}

	//Check if page is in maintenance mode
	if($siteController->ifMaintenance()) {
		include_once("../site/errors/Maintenance.php");
	} else { 
		
		//Set code paste page
		if($siteController->getCurrentProcess() == "paste") {

			if ($siteController->getCurrentMethod() == "save") {
				include_once("../site/components/paste/save.php");
			} else if (isset($_GET["method"]) && $_GET["method"] == "view") {
				include_once("../site/components/paste/view.php");
			} else {
				include_once("../site/components/paste/index.php");
			}	
		}
		

		//Set logs disabler
		else if($siteController->getCurrentProcess() == "disableLogsForMe") {
			if (!$siteController->isCurrentPageAdmin()) {
				$urlUtils->jsRedirect("ErrorHandlerer.php?code=403");
			} else {
				include_once("../site/components/admin/LogsDisabler.php");
			}
		}

		

		//Set image viewer
		else if($siteController->getCurrentProcess() == "image") {
			include_once("../site/components/ImageViewer.php");
		}


		
		//Set api manager
		else if($siteController->getCurrentProcess() == "api" or str_starts_with($siteController->getHTTPhost(), "api")) {
			include_once("../site/API.php");
		} 
		
		else {
			//Check if page loaded with valid url
			if (($siteController->getHTTPhost() != $pageConfig->getValueByName("url")) && $siteController->getHTTPhost() != "localhost") {
				$urlUtils->redirect("ErrorHandlerer.php?code=400");
			}
			//Check if page running on https
			else if ($pageConfig->getValueByName("https") == true && !$mainUtils->isSSL() && $siteController->getHTTPhost() != "localhost") {
				$urlUtils->redirect("ErrorHandlerer.php?code=400");
			} 
				
			//Include main page file
			else {
				include_once("../site/Main.php");
			}
		}
	}
?>
