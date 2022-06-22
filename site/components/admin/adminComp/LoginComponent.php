<?php //The main admin user login component 

	//Add header nav to site
	include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/navigation/HeaderElement.php');
?>	
<div class="loginPage">
<?php 

	//If user table is empty print warning
	if ($adminController->isUserEmpty()) {
		$alertController->flashWarning('Users table is empty<br>Please add admin user manually <strong/><a href="index.php?page=register">here</a></strong>');
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

					//Set session login
					$adminController->setLoginSession($username);

					//Set role session
					$sessionUtils->setSession("role", $mysqlUtils->readFromMysql("SELECT role FROM users WHERE username = '".$_SESSION["username"]."'", "role"));

					//Check if user stay logged in
					if ($saveAccount) {
						$adminController->setLoginCookies($username);
					} else {
						$adminController->unSetLoginCookies($username);
					}

					//log to mysql
					$mysqlUtils->logToMysql("Login", "User $username logged in success");

					//Redirect to admin page
					$urlUtils->redirect("index.php?page=admin");
				} else {

					//log to mysql
					if (empty($mysqlUtils->escapeString($_POST["password"], true, true)) or empty($username)) {
						$mysqlUtils->logToMysql("Login", "Trying to login with empty values");
					} else {
						$mysqlUtils->logToMysql("Login", "Trying to login with name: $username ", true, true);				
					}

					//Print error msg
					$alertController->flashError("Incorrect username or password.");
				}
			}

		}
	}

	//include login form
	include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/forms/LoginForm.php');
?>
</div>
<?php //Add footer to site
	include($_SERVER['DOCUMENT_ROOT'].'/../site/components/elements/navigation/FooterElement.php');
?>
