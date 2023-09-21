<?php  
	http_response_code(429);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <title>Error 429, Too Many Requests</title>
</head>
<body>    
    <p class="error-page-msg">
        <strong>
            Too Many Requests 
            <br><br>
            Please try to wait and try again later
        </strong>
    </p>
	<!-- init fluid animation -->
	<canvas></canvas>
	<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>