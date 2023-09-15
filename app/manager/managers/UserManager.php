<?php // user functions (login, logout, etc...)

	namespace becwork\managers;

	class UserManager {

		// check if users table not empty
		public function is_users_empty(): bool {

			global $mysql;

			// default state output
			$state = false;

			// get users data
			$users = $mysql->fetch("SELECT * FROM users");

			// count users
			$users_count = count($users);

			// check if user is empty
			if ($users_count < 1) {
				$state = true;
			}

			return $state;
		}
		
		// check if user logged in
		public function is_logged_in(): bool {

			global $session_utils, $config;

			// default state output
			$state = false;

			// start session
			$session_utils->start();

			// check if login cookie seted
			if (isset($_SESSION[$config->get_value('login-cookie')])) {
				
				// check if login cookie is valid
				if ($_SESSION[$config->get_value('login-cookie')] == $config->get_value('login-value')) {
					$state = true; // this is logged in state
				} 
			}

			return $state;
		} 

		// check if user can login with username and password
		public function can_login($username, $password): bool {

			global $mysql;

			// default state output
			$state = false;

			// select ID with valid login data
			$login_select = $mysql->fetch("SELECT id FROM users WHERE username = '$username' and password = '$password'");		

			// check if user with password exist
			if (count($login_select) == 1) {
				$state = true; 
			}

			return $state;
		}

		// unset login cookie
		public function unset_login_cookies(): void {

			global $cookie_utils, $config;

			// unset login key cookie
			$cookie_utils->unset($config->get_value("login-cookie"));

			// unset token
			$cookie_utils->unset("user-token");			
		}

		// set login cookie
		public function set_login_cookies($token): void {

			global $cookie_utils, $config;

			// set username cookie for next auth
			$cookie_utils->set("user-token", $token, time() + (60*60*24*7*365));

			// set token cookie for next login
			$cookie_utils->set($config->get_value("login-cookie"), $config->get_value("login-value"), time() + (60*60*24*7*365));			
		}

		// set anti log cookie
		public function set_anti_log_cookie(): void {

			global $cookie_utils, $config;

			// set antilog cookie
			$cookie_utils->set($config->get_value("anti-log-cookie"), $config->get_value("anti-log-value"), time() + (60*60*24*7*365));			
		}

		// set login session
		public function set_login_session($token): void {

			global $session_utils, $config;

			// start session
			$session_utils->start();

			// set token session
			$session_utils->set($config->get_value("login-cookie"), $config->get_value("login-value"));		

			// set token session
			$session_utils->set("user-token", $token);
		}

		// logout user
		public function logout(): void {

			global $mysql, $cookie_utils, $config, $session_utils, $url_utils;
			
			// get username
			$username = $this->get_username();

			// destroy all sessions
			$session_utils->destroy();

			// unset login key cookie
			$cookie_utils->unset($config->get_value("login-cookie"));

			// unset username
			$cookie_utils->unset("user-token");

			// log logout to mysql dsatabase 
			if (!empty($username)) {

				$mysql->log("authenticator", "user ".$username." logout out of admin site");
			}

			// redirect to index page
			$url_utils->redirect("?admin=login");			
		}

		// update password
		public function update_password($username, $password): void {

			global $mysql, $hash_utils;

			// generate hash from password
			$password = $hash_utils->gen_main_hash($password);

			// update password
			$mysql->insert("UPDATE users SET password = '$password' WHERE username = '$username'");

			$mysql->log("profile-update", "update password for user: $username");
		}

		// update profile image
		public function update_profile_image($base64_final, $username): void {

			global $mysql;

			// update image in mysql
			$mysql->insert("UPDATE users SET image_base64 = '$base64_final' WHERE username = '$username'");
        
			$mysql->log("profile-update", "user: $username updated avatar image");
		}

		// check if user is owner
		public function is_user_Owner(): bool {
			
			// default state output
			$state = false;

			// get user role
			$userRole = $this->get_role();

			// get user role to lower case
			$userRole = strtolower($userRole);

			// check if user is owner
			if($userRole == "owner") {
				$state = true;
			}

			return $state;
		}

		// get current username form session
		public function get_username(): ?string {

			global $mysql;

			// default username value
			$username = null;

			// get user token value
			$user_token = $this->get_token();

			// check if user token is not null
			if ($user_token != null) {
				
				// get username
				$username = $mysql->fetch_value("SELECT username FROM users WHERE token = '".$user_token."'", "username");
			}

			return $username;
		}

		// get user role form session
		public function get_role(): string {

			global $mysql;

			// default role value
			$role = null;

			// check if user token is not null
			if ($this->get_token() != null) {

				// return user role
				$role = $mysql->fetch_value("SELECT role FROM users WHERE token = '".$this->get_token()."'", "role");
			} else {
				$this->logout();
			}

			return $role;
		}
		
		//Get user token
		public function get_token(): ?string {

			global $mysql;

			// default token value
			$token = null;

			// check if user token in session
			if (!empty($_SESSION["user-token"])) {
				
				// get ids where token 
				$ids = $mysql->fetch("SELECT id FROM users WHERE token='".$_SESSION["user-token"]."'");
				
				// check if token exist in users
				if (count($ids) > 0) {
					$token = $_SESSION["user-token"];
				} 
			}

			return $token;
		}

		// get user ip
		public function get_user_ip_by_token($token): string {

			global $mysql;

			// default user ip
			$user_ip = null;

			// get ids where token 
			$ids = $mysql->fetch("SELECT id FROM users WHERE token='".$_SESSION["user-token"]."'");
				
			// check if token found
			if (count($ids) > 0) {
				
				// get ip by token
				$ip = $mysql->fetch_value("SELECT remote_addr FROM users WHERE token = '".$token."'", "remote_addr");

				// save to user ip
				$user_ip = $ip;
			}

			return $user_ip;
		}

		// auto user login (for cookie login)
		public function auto_login(): void {
			
			global $mysql, $config, $session_utils, $main_utils, $url_utils;

			// get user token
			$user_token = $_COOKIE["user-token"];

			// get user ip
			$user_ip = $main_utils->get_remote_adress();

			// start session
			$session_utils->start();

			// set login identify session
			$session_utils->set($config->get_value('login-cookie'), $_COOKIE[$config->get_value('login-cookie')]);
 
			// set token session
			$session_utils->set("user-token", $user_token);

			// log action to mysql
			$mysql->log("authenticator", "user: ".$this->get_username()." success login by login cookie");

			// update user ip
			$mysql->insert("UPDATE users SET remote_addr='$user_ip' WHERE token='$user_token'");

			// refresh page
			$url_utils->redirect("?admin=dashboard");
		}

		// get user avara base64 code
		public function get_avatar(): string {

			global $mysql;

			// default avatar value
			$avatar = null;

			// get user token
			$token = $this->get_token();

			// check if user token is not null
			if ($token != null) {

				// get user profile pic (base64 code)
				$avatar = $mysql->fetch_value("SELECT image_base64 FROM users WHERE token = '".$token."'", "image_base64");
			} else {
				$this->logout();
			}

			return $avatar;
		}
	}
?>