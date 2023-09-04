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
	$userManager = new becwork\managers\UserManager();
	$dashboardManager = new becwork\managers\DashboardManager();
	$visitorManager = new becwork\managers\VisitorSystemManager();
	$contactManager = new becwork\managers\ContactManager();
	$todosManager = new becwork\managers\TodosManager();
	$siteManager = new becwork\managers\SiteManager();
	$servicesManager = new becwork\managers\ServicesManager();
	$projectsManager = new becwork\managers\ProjectsManager();
	$alertManager = new becwork\managers\AlertManager();
?>