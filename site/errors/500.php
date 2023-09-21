<?php  
	http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/error-page.css" rel="stylesheet">
	<link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <title>Error: 500, Internal Server Error</title>
</head>
<body>    
    <p class="error-page-msg">
        <strong>
            The server encountered an unexpected condition that prevented it from fulfilling the request
            <br><br>
            Please check the address bar or contact your administrator
        </strong>
    </p>
	<!-- init fluid animation -->
	<canvas></canvas>
	<script src="/assets/js/fluid-animation.js"></script>
</body>
</html>