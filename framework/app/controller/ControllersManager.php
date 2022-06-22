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
	require_once(__DIR__."/controllers/EncryptController.php");
	require_once(__DIR__."/controllers/AlertController.php");

	//Init ContactController
	$contactController = new ContactController();

	//Init AdminController
	$adminController = new AdminController();

	//Init TodosController
	$todosController = new TodosController();

	//Init PageConfigController
	$pageConfigController = new PageConfigController();

	//Init DashboardController
	$dashboardController = new DashboardController();

	//Init ApiController 
	$apiController = new ApiController();

	//Init SiteController
	$siteController = new SiteController();

	//Init VisitorSystemController
	$visitorController = new VisitorSystemController();

	//Init ServicesController
	$servicesController = new ServicesController();

	//Init AlertController
	$alertController = new AlertController();

	//Init EncryptController
	$encryptController = new EncryptController();
?>