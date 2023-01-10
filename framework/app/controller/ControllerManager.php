<?php //This is the main class to include all active controllers 

    //require all controllers path
	require_once(__DIR__."/controllers/ContactController.php");
	require_once(__DIR__."/controllers/AdminController.php");
	require_once(__DIR__."/controllers/TodosController.php");
	require_once(__DIR__."/controllers/PageConfigController.php");
	require_once(__DIR__."/controllers/DashboardController.php");
	require_once(__DIR__."/controllers/ApiController.php");
	require_once(__DIR__."/controllers/SiteController.php");
	require_once(__DIR__."/controllers/VisitorSystemController.php");
	require_once(__DIR__."/controllers/ServicesController.php");
	require_once(__DIR__."/controllers/AlertController.php");
	require_once(__DIR__."/controllers/ProjectsController.php");

	//Init ContactController
	$contactController = new becwork\controllers\ContactController();

	//Init AdminController
	$adminController = new becwork\controllers\AdminController();

	//Init TodosController
	$todosController = new becwork\controllers\TodosController();

	//Init PageConfigController
	$pageConfigController = new becwork\controllers\PageConfigController();

	//Init DashboardController
	$dashboardController = new becwork\controllers\DashboardController();

	//Init ApiController 
	$apiController = new becwork\controllers\ApiController();

	//Init SiteController
	$siteController = new becwork\controllers\SiteController();

	//Init VisitorSystemController
	$visitorController = new becwork\controllers\VisitorSystemController();

	//Init ServicesController
	$servicesController = new becwork\controllers\ServicesController();

	//Init AlertController
	$alertController = new becwork\controllers\AlertController();

	//Init ProjectsController
	$projectsController = new becwork\controllers\ProjectsController();
?>