<?php // this is the main class to include all active controllers 

    // require all controllers path
	require_once(__DIR__."/controllers/ContactController.php");
	require_once(__DIR__."/controllers/UserController.php");
	require_once(__DIR__."/controllers/TodosController.php");
	require_once(__DIR__."/controllers/PageConfigController.php");
	require_once(__DIR__."/controllers/DashboardController.php");
	require_once(__DIR__."/controllers/SiteController.php");
	require_once(__DIR__."/controllers/VisitorSystemController.php");
	require_once(__DIR__."/controllers/ServicesController.php");
	require_once(__DIR__."/controllers/AlertController.php");
	require_once(__DIR__."/controllers/ProjectsController.php");

	// ContactController
	$contactController = new becwork\controllers\ContactController();

	// AdminController
	$userController = new becwork\controllers\UserController();

	// TodosController
	$todosController = new becwork\controllers\TodosController();

	// PageConfigController
	$configController = new becwork\controllers\PageConfigController();

	// DashboardController
	$dashboardController = new becwork\controllers\DashboardController();

	// SiteController
	$siteController = new becwork\controllers\SiteController();

	// VisitorSystemController
	$visitorController = new becwork\controllers\VisitorSystemController();

	// ServicesController
	$servicesController = new becwork\controllers\ServicesController();

	// AlertController
	$alertController = new becwork\controllers\AlertController();

	// ProjectsController
	$projectsController = new becwork\controllers\ProjectsController();
?>