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
	
	// init mysql controller
	$mysql = new becwork\utils\MysqlUtils();

	// check if composer installed
	if(file_exists('../vendor/autoload.php')) {
		require_once('../vendor/autoload.php');	
	} else {
		
		// handle error redirect to error page if composer components is not installed
		$site_manager->handle_error("vendor directory not found, plese run composer install", 500);
	} 

	// init whoops for error headling
	if ($site_manager->is_dev_mode()) {
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();
	}	

	// init detect mobile lib
	$mobileDetector = new Detection\MobileDetect;	

	// include main site component
	include_once("../site/Main.php");
	