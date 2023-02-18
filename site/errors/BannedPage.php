<?php  
	http_response_code(403);

	// include config
	include_once("../config.php");
	$configOBJ = new becwork\config\PageConfig();

	// include mysql utils
	include_once("../framework/mysql/MysqlUtils.php");
	$mysqlUtils = new becwork\utils\MysqlUtils();

	// include main utils
	include_once("../framework/utils/MainUtils.php");
	$mainUtils = new becwork\utils\MainUtils();
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
			<h3 class="errorPageMSG"><strong>You are permanently banned on this site.<br>
			<?php
				echo "<br>";
				// get current ip
				$ip = $mainUtils->getRemoteAdress();

				// get IDs where ip
				$ids = $mysqlUtils->fetch("SELECT id FROM banned WHERE `ip_adress`='$ip'");

				// check if banned found
				if (count($ids) > 0) {
					// print reason if found
					if ($mysqlUtils->fetchValue("SELECT reason FROM banned WHERE ip_adress = '$ip'", "reason") != "no reason") {
						echo "REASON: ".$mysqlUtils->fetchValue("SELECT reason FROM banned WHERE ip_adress = '$ip'", "reason");
					} 
				}
			?>
			<br><br>Please direct unban requests to <?php echo $pageConfig->getValueByName("email"]; ?></strong></h3>
		</center>
	</main>
</body>
</html>
