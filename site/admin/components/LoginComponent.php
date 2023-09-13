<div class="login-page">
<?php 
	// user table is empty print warning
	if ($user_manager->is_users_empty()) {
		$alert_manager->flash_warning('Users table is empty<br>Please add admin user manually <strong/><a href="?admin=login&action=register">here</a></strong>');
	}

	// check if user not logged in and if submit login button
	if (!$user_manager->is_logged_in() and isset($_POST["submitLogin"])) {

		// honeypot check
		if (!empty($_POST["website"])) {
			$site_manager->redirect_error(400);

		} else {

			// init values
			$username = $escape_utils->special_chars_strip($_POST["username"]);
			$password_raw = $escape_utils->special_chars_strip($_POST["password"]);

			// hash password
			$password = $hash_utils->gen_main_hash($password_raw);

			// default save account
			$save_account = false;

			// save account set true if user send value
			if (isset($_POST["saveAccount"])) {
				$save_account = true;
			} 

			// check if values not empty
			if (empty($username) or empty($password)) {
				$alert_manager->flash_error("Incorrect username or password.");

			} else {

				// check if user can login with our values
				if ($user_manager->can_login($username, $password)) {

					// get user token
					$token = $mysql->fetch_value("SELECT token FROM users WHERE username = '".$username."'", "token");

					// check if token is seted
					if (!empty($token)) {

						// set session login
						$user_manager->set_login_session($token);

						// set role session
						$session_utils->set("role", $mysql->fetch_value("SELECT role FROM users WHERE token = '".$token."'", "role"));

						// check if user stay logged in
						if ($save_account) {
							$user_manager->set_login_cookies($token);
						} else {
							$user_manager->unset_login_cookies();
						}

						// get user ip
						$user_ip = $main_utils->get_remote_adress();

						// update user ip
						$mysql->insert("UPDATE users SET remote_addr='$user_ip' WHERE username='$username'");

						// log to mysql
						$mysql->log("authenticator", "user: $username logged in success");

						// redirect to admin page
						$url_utils->redirect("?admin=dashboard");

					} else {

						// devmode error print
						if ($site_manager->is_dev_mode()) {
							die("<h2 class=page-title>[DEV-MODE]:Login, error user token is empty</h2>");
						} 
						
						// error redirect
						else {
							include_once("errors/UnknownError.php");
						}						
					}
				} else {

					// print error msg
					$alert_manager->flash_error("Incorrect username or password.");
			
					// log to mysql
					if (empty($username) or empty($password_raw)) {
						$mysql->log("authenticator", "trying to login with empty values");
					} else {
						$mysql->log("authenticator", "trying to login with name: $username:$password_raw");				
					}
				}
			}
		}
	}

	// login form
	include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/LoginForm.php');
?>
</div>