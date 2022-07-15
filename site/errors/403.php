<?php  
	http_response_code(403);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>
	<title>Error 403, Forbidden</title>
</head>
<style>
	* { 
		padding: 0px;
		margin: 0px;
		box-sizing: border-box;
	}

	body {
		background: rgb(12, 12, 12);
	}
	.mainPage {
		position: fixed;
		width: 100%;
		height: 100%;
	}

	.errorPageMSG {
		color: white;
		font-size: 20px;
		position: absolute;
		top: 45%;
		left: 50%;
		width: 100%;
		transform: translate(-50%, -50%);
		font-family: 'Maven Pro', sans-serif;
		user-select: none;
		opacity: 0.8;
	}
</style>
<body class="mainPage">
	<main>
		<center>
			<h3 class="errorPageMSG">
				<strong>You do not have permission to access this page</strong>
				<br><br><br><br><br><br><br><br>
			</h3>
			<h3 class="errorPageMSG">
				<strong>Please check the address bar or contact your administrator</strong>
			</h3>
		</center>
	</main>
</body>
</html>