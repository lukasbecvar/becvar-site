<?php  
	http_response_code(403);

	// include config
	include_once("../config.php");
	$configOBJ = new becwork\config\PageConfig();

	// include visitors controller
	include_once("../framework/app/controller/controllers/VisitorSystemController.php");
	$visitorController = new becwork\controllers\VisitorSystemController();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
	<link href="/assets/css/error-page.css" rel="stylesheet">
	<title>You are banned</title>
</head>
<body class="mainPage">
	<main>
		<center>
			<h3 class="errorPageMSG"><strong>You are permanently banned on this site.<br><br>

			Ban reason: Rule violations or bot detection

			<br><br>Please direct unban requests to <?php echo $configOBJ->config["email"]; ?></strong></h3>
		</center>
	</main>
</body>
</html>
