<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-------------------- Import assets ------------------------>
	<link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/mainAdmin.css">
	<script type="text/javascript" src="assets/vendor/jquery/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="assets/vendor/fontawesome/fontawesome.min.css">
	<?php //Import gallery css if user browsing media
		if ($siteController->getCurrentAdminProcess() == "mediaBrowser") {
			echo '<link href="assets/vendor/lightgallery/css/lightgallery.css" rel="stylesheet">';	
			echo '<link href="assets/css/assets/vendor/lightgallery/css/lg-transitions.css" rel="stylesheet">';	
		}
	?>
    <!-------------------- End of assets import ------------------------>
	<title>
		<?php //Print page title 
			echo $siteController->getPageTitle();
		?>
	</title>
</head>
<body>

<?php //20% unzoom on non mobile device
    if(!$mobileDetector->isMobile()) {
        echo '
            <script>
                document.body.style.zoom = "80%";
            </script>
        ';
    }
?>

<main class="adminPage"> 
<div class="wrapper">
<?php //Main site redirector

	//Check if user send logout request
	if ($siteController->getCurrentAction() == "logout") {
        $adminController->logout();

    //Check if user send register action
    } else if ($siteController->getCurrentAction() == "register") {
        include_once("components/AdminAccountRegisterComponent.php");

    } else { 

        //////////////////////////////////////////////////////////////////////////////////////////////////

        //Check if user logged in
        if ($adminController->isLoggedIn()) {	

            //Include admin top nav bar
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/TopPanel.php');


            //Include admin sidebar
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/Sidebar.php');


            //Define process by name
            if ($siteController->getCurrentAdminProcess() == "dashboard") {
                include_once("components/DashboardComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "accountSettings") {
                include_once("components/AccountSettingsComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "inbox") {
                include_once("components/InboxComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "todos") {
                include_once("components/TodoManager.php");

            } elseif ($siteController->getCurrentAdminProcess() == "pageSettings") {
                include_once("components/PageSettings.php");

            } elseif ($siteController->getCurrentAdminProcess() == "phpInfo") {
                include_once("components/phpInfoComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "diagnostics") {
                include_once("components/DiagnosticsComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "dbBrowser") {
                include_once("components/DatabaseBrowserComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "logReader") {
                include_once("components/LogReaderComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "visitors") {
                include_once("components/VisitorsManagerComponent.php");

            } elseif ($siteController->getCurrentAdminProcess() == "mediaBrowser") {
                include_once("components/MediaBrowserComponent.php");


            //Login admin action redirect logged in users
            } elseif ($siteController->getCurrentAdminProcess() == "login") {
                $urlUtils->jsRedirect("/?admin=dashboard");

            //Task executor
            } elseif ($siteController->getCurrentAdminProcess() == "executeTask") {
                include_once("CommandExecutor.php");
                
            //Redirect to 404 if process not found or print error for dev mode
            } else {
                if ($pageConfig->getValueByName("dev_mode") == true) {
                    die("<h2 class=pageTitle>[DEV-MODE]:Error: process: ".$siteController->getCurrentAdminProcess()." not found<h2>");
                } else {
                    $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
                }
            }

        } else {

            //Auto login if user have token cookie
            if (isset($_COOKIE[$pageConfig->getValueByName('loginCookie')]) and isset($_COOKIE["userToken"])) {

                //Check if token valid
                if ($_COOKIE[$pageConfig->getValueByName('loginCookie')] == $pageConfig->getValueByName('loginValue')) {

                    //Auto user login
                    $adminController->autoLogin();

                } else {
                    //Set login action if user not logged in
                    include_once("components/LoginComponent.php");
                }
            } else {
                //Set login action if user not logged in
                include_once("components/LoginComponent.php");
            }
        }

        //Sidebar menu toggle script includer
        if(!$mobileDetector->isMobile()) {

            if($siteController->getCurrentAdminProcess() != "dashboard") {
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/functional/NavPanelToggler.php');
            }	

        } else {
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/functional/NavPanelToggler.php');;		
        }

        //////////////////////////////////////////////////////////////////////////////////////////////////
	}
?>
</main>
<!----------------------- Import scripts ---------------------------->
<script type="text/javascript" src="assets/vendor/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/vendor/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="assets/js/mainAdmin.js"></script>
<!-------------------- End of import scripts ------------------------>
</body>
</html>