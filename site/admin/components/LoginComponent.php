<div class="loginPage">
<?php 
	// user table is empty print warning
	if ($userController->isUserEmpty()) {
		$alertController->flashWarning('Users table is empty<br>Please add admin user manually <strong/><a href="?admin=login&action=register">here</a></strong>');
	}

	// check if user not logged in and if submit login button
	if (!$userController->isLoggedIn() and isset($_POST["submitLogin"])) {

		// honeypot check
		if (!empty($_POST["website"])) {
			$siteController->redirectError(400);

		} else {

			// init values
			$username = $escapeUtils->specialCharshStrip($_POST["username"]);
			$passwordRaw = $escapeUtils->specialCharshStrip($_POST["password"]);

			// hash password
			$password = $hashUtils->genBlowFish($passwordRaw);

			// default save account
			$saveAccount = false;

			// save account set true if user send value
			if (isset($_POST["saveAccount"])) {
				$saveAccount = true;
			} 

			// check if values not empty
			if (empty($username) or empty($password)) {
				$alertController->flashError("Incorrect username or password.");

			} else {

				// check if user can login with our values
				if ($userController->canLogin($username, $password)) {

					// get user token
					$token = $mysql->fetchValue("SELECT token FROM users WHERE username = '".$username."'", "token");

					// check if token is seted
					if (!empty($token)) {

						// set session login
						$userController->setLoginSession($token);

						// set role session
						$sessionUtils->setSession("role", $mysql->fetchValue("SELECT role FROM users WHERE token = '".$token."'", "role"));

						// check if user stay logged in
						if ($saveAccount) {
							$userController->setLoginCookies($token);
						} else {
							$userController->unSetLoginCookies();
						}

						// get user ip
						$userIP = $mainUtils->getRemoteAdress();

						// update user ip
						$mysql->insertQuery("UPDATE users SET remote_addr='$userIP' WHERE username='$username'");

						// log to mysql
						$mysql->logToMysql("Success login", "User $username logged in success");

						// redirect to admin page
						$urlUtils->redirect("?admin=dashboard");

					} else {

						// devmode error print
						if ($siteController->isSiteDevMode()) {
							die("<h2 class=pageTitle>[DEV-MODE]:Login, error user token is empty</h2>");
						} 
						
						// error redirect
						else {
							include_once("errors/UnknownError.php");
						}						
					}
				} else {

					// print error msg
					$alertController->flashError("Incorrect username or password.");
			
					// log to mysql
					if (empty($username) or empty($passwordRaw)) {
						$mysql->logToMysql("Login", "Trying to login with empty values");
					} else {
						$mysql->logToMysql("Login", "Trying to login with name: $username:$passwordRaw");				
					}
				}
			}
		}
	}

	// login form
	include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LoginForm.php');
?>
</div>