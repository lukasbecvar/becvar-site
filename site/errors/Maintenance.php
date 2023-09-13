<?php  
	http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <title>Error: 503, page is unavailable</title>
</head>
<body>    
	<p class="error-page-msg">
		<strong>
			The service is temporarily unavailable due to maintenance
		</strong>
	</p>
	<!-- init fluid animation -->
	<canvas></canvas>
	<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>