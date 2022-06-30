<div class="loginPage">
<?php 
	//If user table is empty print warning
	if ($adminController->isUserEmpty()) {
		$alertController->flashWarning('Users table is empty<br>Please add admin user manually <strong/><a href="?admin=login&action=register">here</a></strong>');
	}

	//Check if user not logged in and if submit login button
	if (!$adminController->isLoggedIn() and isset($_POST["submitLogin"])) {

		//honeypot check
		if (!empty($_POST["website"])) {
			$urlUtils->jsRedirect("ErrorHandlerer.php?code=400");

		} else {

			//Init values
			$username = $mysqlUtils->escapeString($_POST["username"], true, true);
			$password = $mysqlUtils->escapeString($_POST["password"], true, true);

			//Hash password
			$password = $hashUtils->genBlowFish($password);

			//Default save account
			$saveAccount = false;

			//Save account set true if user send value
			if (isset($_POST["saveAccount"])) {
				$saveAccount = true;
			} 

			//Check if values not empty
			if (empty($username) or empty($password)) {
				$alertController->flashError("Incorrect username or password.");

			} else {

				//Check if user can login with our values
				if ($adminController->canLogin($username, $password)) {

					//Get user token
					$token = $mysqlUtils->readFromMysql("SELECT token FROM users WHERE username = '".$username."'", "token");

					//Check if token is seted
					if (!empty($token)) {

						//Set session login
						$adminController->setLoginSession($token);

						//Set role session
						$sessionUtils->setSession("role", $mysqlUtils->readFromMysql("SELECT role FROM users WHERE token = '".$token."'", "role"));

						//Check if user stay logged in
						if ($saveAccount) {
							$adminController->setLoginCookies($token);
						} else {
							$adminController->unSetLoginCookies();
						}

						//log to mysql
						$mysqlUtils->logToMysql("Success login", "User $username logged in success");

						//Redirect to admin page
						$urlUtils->redirect("?admin=dashboard");

					} else {
						if ($pageConfig->getValueByName("dev_mode") == true) {
							die("<h2 class=pageTitle>[DEV-MODE]:Login, error user token is empty</h2>");
						} else {
							include_once("errors/UnknownError.php");
						}						
					}
				} else {

					//Print error msg
					$alertController->flashError("Incorrect username or password.");
				}
			}

			//log to mysql
			if (empty($username) or empty($password)) {
				$mysqlUtils->logToMysql("Login", "Trying to login with empty values");
			} else {
				$mysqlUtils->logToMysql("Login", "Trying to login with name: $username ", true, true);				
			}

		}
	}

	//include login form
	include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LoginForm.php');
?>
</div>