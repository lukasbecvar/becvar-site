<main class="adminPage"> 
<div class="wrapper">
<?php //This is main file of admin site

	//Check if user logged in
	if ($adminController->isLoggedIn()) {	

		//Set default process if is empty
		if ($siteController->isProcessEmpty()) {
			$urlUtils->redirect("index.php?page=admin&process=dashboard");
		}

		//Include admin top nav bar
		include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/admin/AdminTopPanel.php');


		//Include admin sidebar
		include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/admin/AdminSidebar.php');


		//Define process by name
		if ($siteController->getCurrentProcess() == "dashboard") {
			include_once("adminComp/DashboardComponent.php");

		} elseif ($siteController->getCurrentProcess() == "accountSettings") {
			include_once("adminComp/AccountSettingsComponent.php");

		} elseif ($siteController->getCurrentProcess() == "inbox") {
			include_once("adminComp/InboxComponent.php");

		} elseif ($siteController->getCurrentProcess() == "todos") {
			include_once("adminComp/TodoManager.php");

		} elseif ($siteController->getCurrentProcess() == "pageSettings") {
			include_once("adminComp/PageSettings.php");

		} elseif ($siteController->getCurrentProcess() == "phpInfo") {
			include_once("adminComp/phpInfoComponent.php");

		} elseif ($siteController->getCurrentProcess() == "dbBrowser") {
			include_once("adminComp/DatabaseBrowserComponent.php");

		} elseif ($siteController->getCurrentProcess() == "logReader") {
			include_once("adminComp/LogReaderComponent.php");

		} elseif ($siteController->getCurrentProcess() == "mediaBrowser") {
			include_once("adminComp/MediaBrowserComponent.php");
				



		//Task executor
		} elseif ($siteController->getCurrentProcess() == "executeTask") {
			include_once("CommandExecutor.php");

			
		//Redirect to 404 if process not found or print error for dev mode
		} else {
			if ($pageConfig->getValueByName("dev_mode") == true) {
				die("<h2 class=pageTitle>[DEV-MODE]:Error: process: ".$siteController->getCurrentProcess()." not found<h2>");
			} else {
				$urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
			}
		}

	} else {

		//Auto login if user have token cookie
		if (isset($_COOKIE[$pageConfig->getValueByName('loginCookie')]) and isset($_COOKIE["username"])) {

			//Check if token valid
			if ($_COOKIE[$pageConfig->getValueByName('loginCookie')] == $pageConfig->getValueByName('loginValue')) {

				//Auto user login
				$adminController->autoLogin();

			} else {
				//Set login action if user not logged in
				include_once("adminComp/LoginComponent.php");
			}
		} else {
			//Set login action if user not logged in
			include_once("adminComp/LoginComponent.php");
		}
	}

	//Sidebar menu toggle script includer
	if(!$mobileDetector->isMobile()) {

		if($siteController->getCurrentProcess() != "dashboard") {
			include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/functional/NavPanelToggler.php');
		}	

	} else {
		include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/functional/NavPanelToggler.php');;		
	}
?>
</main>