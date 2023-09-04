<?php // this is the main class to include all active managers 

    // require all managers path
	require_once(__DIR__."/managers/ContactManager.php");
	require_once(__DIR__."/managers/UserManager.php");
	require_once(__DIR__."/managers/TodosManager.php");
	require_once(__DIR__."/managers/PageConfigManager.php");
	require_once(__DIR__."/managers/DashboardManager.php");
	require_once(__DIR__."/managers/SiteManager.php");
	require_once(__DIR__."/managers/VisitorSystemManager.php");
	require_once(__DIR__."/managers/ServicesManager.php");
	require_once(__DIR__."/managers/AlertManager.php");
	require_once(__DIR__."/managers/ProjectsManager.php");

	// ContactManager
	$contactManager = new becwork\managers\ContactManager();

	// AdminManager
	$userManager = new becwork\managers\UserManager();

	// TodosManager
	$todosManager = new becwork\managers\TodosManager();

	// PageConfigManager
	$configManager = new becwork\managers\PageConfigManager();

	// DashboardManager
	$dashboardManager = new becwork\managers\DashboardManager();

	// SiteManager
	$siteManager = new becwork\managers\SiteManager();

	// VisitorSystemManager
	$visitorManager = new becwork\managers\VisitorSystemManager();

	// ServicesManager
	$servicesManager = new becwork\managers\ServicesManager();

	// AlertManager
	$alertManager = new becwork\managers\AlertManager();

	// ProjectsManager
	$projectsManager = new becwork\managers\ProjectsManager();
?>