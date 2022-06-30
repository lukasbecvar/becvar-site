<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>
		<?php //Print page title 
			echo $siteController->getPageTitle();
		?>
	</title>
    <?php //Add google token
        echo '<meta name="google-site-verification" content="'.$pageConfig->getValueByName("googleVerifyToken").'"/>';  
    ?>
    <meta content="<?php echo $pageConfig->getValueByName("siteDescription"); ?>" name="description">
    <meta content="<?php echo $pageConfig->getValueByName("siteKeywords"); ?>" name="keywords">

    <!-------------------- Import assets ------------------------>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/css/mainPublic.css" rel="stylesheet">
    <!-------------------- End of assets import ------------------------>
</head>
<body>
    <?php //Include components & elements to site 
    
        //Include main header element
        include_once("elements/MainElement.php");

        //Include about component
        include_once("components/AboutComponent.php");

        //Include projects component
        include_once("components/ProjectsComponent.php");
 
        //Include services element
        include_once("elements/ServicesElement.php");

        //Include contact component
        include_once("components/ContactComponent.php");

        //Include uploader component
        include_once("components/ImageUploaderComponent.php");

        //Include generator component
        include_once("components/GeneratorComponent.php");
    ?>
    <!----------------------- Import scripts ---------------------------->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery/jquery-3.6.0.min.js"></script>
    <script src="assets/js/mainPublic.js"></script> 
    <!-------------------- End of import scripts ------------------------>
</body>
</html>