<?php  
	http_response_code(403);

	// include config
	include_once("../config.php");
	$configOBJ = new becwork\config\PageConfig();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <title>You are banned</title>
</head>
<body>    
	<p class="error-page-msg">
		<strong>
			You are permanently banned on this site.
			<br><br>
			Please direct unban requests to <?php echo $configOBJ->config["email"]; ?>
		</strong>
	</p>
	<!-- init fluid animation -->
	<canvas></canvas>
	<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>