<?php
	http_response_code(520);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <title>Error: vendor not found</title>
</head>
<body>    
	<p class="errorPageMSG">
		<strong>
			[DEV-MODE]
			<br><br>
			Error: vendor/ not exist please install composer components.
		</strong>
	</p>
	<!-- init fluid animation -->
	<canvas></canvas>
	<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>