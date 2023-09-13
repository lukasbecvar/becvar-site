<?php // main admin initor

    // get admin component name
    $component = $site_manager->get_query_string("admin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-------------------- import assets ------------------------>
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="/assets/vendor/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/admin.css">
	<script type="text/javascript" src="/assets/vendor/jquery/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="/assets/vendor/fontawesome/fontawesome.min.css">
	<?php // import gallery css if user browsing media
		if ($component == "mediaBrowser") {
			echo '<link href="/assets/vendor/lightgallery/css/lightgallery.css" rel="stylesheet">';	
			echo '<link href="/assets/css/assets/vendor/lightgallery/css/lg-transitions.css" rel="stylesheet">';	
		}
	?>
    <!-------------------- end of assets import ------------------------>
	<title><?= $site_manager->get_page_title() ?></title>
</head>
<body>

<?php 
    // 20% unzoom on non mobile device
    if(!$mobileDetector->isMobile()) {
        echo '
            <script>
                document.body.style.zoom = "80%";
            </script>
        ';
    }

    // default disable sidebar
    if ($component != "dashboard") {
        echo '
            <script>
                document.querySelector("body").classList.toggle("active");
            </script>
        ';
    }
?>

<main class="admin-page"> 
<div class="wrapper">
<?php // main site redirector

	// check if user send logout request
	if ($site_manager->get_query_string("action") == "logout") {
        $user_manager->logout();

    // check if user send register action
    } else if ($site_manager->get_query_string("action") == "register") {
        include_once("components/AdminAccountRegisterComponent.php");

    } else {    

        // check if user logged in
        if ($user_manager->is_logged_in()) {	

            // admin top nav bar
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/TopPanel.php');

            // admin sidebar
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/Sidebar.php');

            // define process by name //////////////////////////////////////////////////////////////
            if ($component == "dashboard") {
                include_once("components/DashboardComponent.php");

            } elseif ($component == "accountSettings") {
                include_once("components/AccountSettingsComponent.php");

            } elseif ($component == "inbox") {
                include_once("components/InboxComponent.php");

            } elseif ($component == "todos") {
                include_once("components/TodoManager.php");

            } elseif ($component == "pageSettings") {
                include_once("components/PageSettings.php");

            } elseif ($component == "phpInfo") {
                include_once("components/phpInfoComponent.php");

            } elseif ($component == "diagnostics") {
                include_once("components/DiagnosticsComponent.php");

            } elseif ($component == "dbBrowser") {
                include_once("components/DatabaseBrowserComponent.php");

            } elseif ($component == "logReader") {
                include_once("components/LogReaderComponent.php");

            } elseif ($component == "visitors") {
                include_once("components/VisitorsManagerComponent.php");

            } elseif ($component == "mediaBrowser") {
                include_once("components/MediaBrowserComponent.php");
            ////////////////////////////////////////////////////////////////////////////////////////

            // init project reload
            } elseif ($component == "projectsReload") {

                // update project list by github
                $projects_manager->update_project_list();

                // redirect back to table
                $url_utils->js_redirect("?admin=dbBrowser&name=projects&limit=".$config->get_value("row-in-table-limit")."&startby=0");

            // login admin action redirect logged in users
            } elseif ($component == "login") {
                $url_utils->js_redirect("/?admin=dashboard");

            // task executor
            } elseif ($component == "executeTask") {
                include_once("CommandExecutor.php");
            
            // show form
            } elseif ($component == "form") {
                include_once("FormHandlerer.php");

            // redirect to 404 if process not found or print error for dev mode
            } else {
                $site_manager->handle_error("error: process: ".$component." not found", 404);
            }

        } else {

            // auto login if user have token cookie
            if (isset($_COOKIE[$config->get_value('login-cookie')]) and isset($_COOKIE["user-token"])) {

                // check if token valid
                if ($_COOKIE[$config->get_value('login-cookie')] == $config->get_value('login-value')) {

                    //Auto user login
                    $user_manager->auto_login();

                } else {
                    // set login action if user not logged in
                    include_once("components/LoginComponent.php");
                }
            } else {
                // set login action if user not logged in
                include_once("components/LoginComponent.php");
            }
        }

        // sidebar menu toggle script includer
        if(!$mobileDetector->isMobile()) {

            // check if is admin process not dashboard
            if($component != "dashboard") {
                include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/functional/NavPanelToggler.php');
            }	
        } else {

            // disable sidebar for dashboard (mobile)
            if ($component == "dashboard") {
                echo '
                    <script>
                        document.querySelector("body").classList.toggle("active");
                    </script>
                ';
            }

            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/functional/NavPanelToggler.php');;		
        }
	}
?>
</main>
<!----------------------- import scripts ---------------------------->
<script type="text/javascript" src="/assets/vendor/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/vendor/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<!-------------------- end of import scripts ------------------------>
</body>
</html>