<?php  
	http_response_code(403);

	// include config
	include_once("../config.php");
	$configOBJ = new becwork\config\PageConfig();
?>
<title>You are banned</title>
<p class="error-page-msg">
	<strong>
		You are permanently banned on this site.
		<br><br>
		Please direct unban requests to <?php echo $configOBJ->config["email"]; ?>
	</strong>
</p>