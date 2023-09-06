<?php // manager init list

    // require all managers path
	require_once(__DIR__."/managers/ContactManager.php");
	require_once(__DIR__."/managers/UserManager.php");
	require_once(__DIR__."/managers/TodosManager.php");
	require_once(__DIR__."/managers/DashboardManager.php");
	require_once(__DIR__."/managers/SiteManager.php");
	require_once(__DIR__."/managers/VisitorSystemManager.php");
	require_once(__DIR__."/managers/ServicesManager.php");
	require_once(__DIR__."/managers/AlertManager.php");
	require_once(__DIR__."/managers/ProjectsManager.php");

	// init all managers objects
	$user_manager = new becwork\managers\UserManager();
	$dashboard_manager = new becwork\managers\DashboardManager();
	$visitor_manager = new becwork\managers\VisitorSystemManager();
	$contact_manager = new becwork\managers\ContactManager();
	$todos_manager = new becwork\managers\TodosManager();
	$site_manager = new becwork\managers\SiteManager();
	$services_manager = new becwork\managers\ServicesManager();
	$projects_manager = new becwork\managers\ProjectsManager();
	$alert_manager = new becwork\managers\AlertManager();
?>