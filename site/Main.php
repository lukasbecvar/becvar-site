<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="robots" content="noindex,nofollow">
	<meta name="Description" CONTENT="Lukáš Bečvář AKA Lordbecvold personal website">
	<link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
	<script type="text/javascript" src="assets/js/jquery_3.2.0.min.js"></script>
	<link rel="stylesheet" href="assets/css/fontawesome.css">

	<?php //Import gallery css if user browsing media
		if ($siteController->getCurrentProcess() == "mediaBrowser" || $siteController->getCurrentPage() == "generator") {
			echo '<link href="assets/css/lightgallery.css" rel="stylesheet">';	
			echo '<link href="assets/css/lg-transitions.css" rel="stylesheet">';	
		}

		//Google site token
		echo '<meta name="google-site-verification" content="'.$pageConfig->getValueByName("googleVerifyToken").'"/>';
	?>

	<title>
		<?php //Print page title 
			echo $siteController->getPageTitle();
		?>
	</title>
</head>
<body>
<?php //Main site redirector

	//Init visitor system
	$visitorController->init();

	//Get page from get url and escaped
	$page = $siteController->getCurrentPage();
	
	//Check if user send logout request
	if ($siteController->getCurrentAction() == "logout") {
		$adminController->logout();	
	} else {

		//Check if get is setted
		if (empty($_GET) or empty($page)) {
			include_once("components/Home.php");
		} else {

			//Redirect to page by name
			if ($page == "admin") {
				include_once("components/admin/InitAdmin.php");

			} else if ($page== "home") {
				include_once("components/Home.php");

			} else if ($page == "about") {
				include_once("components/About.php");

			} else if ($page == "generator") {
				include_once("components/Generator.php");

			} else if ($page == "contact") {
				include_once("components/Contact.php");

			} else if ($page == "register") {
				include_once("components/Register.php");

			} else if ($page == "imageUploader") {
				include_once("components/ImageUploader.php");

			//Redirect to 404 error if page not found
			} else {
				if ($pageConfig->getValueByName("dev_mode") == true) {
					die("<h2 class=pageTitle>[DEV-MODE]:Error: page ".$page." not found</h2>");
				} else {
					include_once("errors/404.php");
				}
			}	 
		}
	}
?>
</body>
<script type="text/javascript" src="assets/js/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap/bootstrap.bundle.min.js"></script>

<?php //The page title text changer
	if ($siteController->getHTTPhost() != "localhost" && !$siteController->isCurrentPageAdmin()) {
		include_once("components/elements/functional/PageTitleChanger.php");
	}
?>
<script type="text/javascript" src="assets/js/main.js"></script>
</html>