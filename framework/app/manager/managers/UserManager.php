<?php // user functions (login, logout, etc...)

	namespace becwork\managers;

	class UserManager {

		// check if users table not empty
		public function isUserEmpty(): bool {

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
		public function isLoggedIn(): bool {

			global $sessionUtils, $config;

			// default state output
			$state = false;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// check if login cookie seted
			if (isset($_SESSION[$config->getValue('login-cookie')])) {
				
				// check if login cookie is valid
				if ($_SESSION[$config->getValue('login-cookie')] == $config->getValue('login-value')) {
					$state = true; // this is logged in state
				} 
			}

			return $state;
		} 

		// check if user can login with username and password
		public function canLogin($username, $password): bool {

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
		public function unSetLoginCookies(): void {

			global $cookieUtils, $config;

			// unset login key cookie
			$cookieUtils->unset_cookie($config->getValue("login-cookie"));

			// unset token
			$cookieUtils->unset_cookie("userToken");			
		}

		// set login cookie
		public function setLoginCookies($token): void {

			global $cookieUtils, $config;

			// set username cookie for next auth
			$cookieUtils->cookieSet("userToken", $token, time() + (60*60*24*7*365));

			// set token cookie for next login
			$cookieUtils->cookieSet($config->getValue("login-cookie"), $config->getValue("login-value"), time() + (60*60*24*7*365));			
		}

		// set anti log cookie
		public function setAntiLogCookie(): void {

			global $cookieUtils, $config;

			// set antilog cookie
			$cookieUtils->cookieSet($config->getValue("anti-log-cookie"), $config->getValue("anti-log-value"), time() + (60*60*24*7*365));			
		}

		// set login session
		public function setLoginSession($token): void {

			global $sessionUtils, $config;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set token session
			$sessionUtils->setSession($config->getValue("login-cookie"), $config->getValue("login-value"));		

			// set token session
			$sessionUtils->setSession("userToken", $token);
		}

		// logout user
		public function logout(): void {

			global $mysql, $cookieUtils, $config, $sessionUtils, $urlUtils;
			
			// get username
			$username = $this->getCurrentUsername();

			// destroy all sessions
			$sessionUtils->sessionDestroy();

			// unset login key cookie
			$cookieUtils->unset_cookie($config->getValue("login-cookie"));

			// unset username
			$cookieUtils->unset_cookie("userToken");

			// log logout to mysql dsatabase 
			if (!empty($username)) {

				// log logout action
				$mysql->logToMysql("authenticator", "user ".$username." logout out of admin site");
			}

			// redirect to index page
			$urlUtils->redirect("?admin=login");			
		}

		// update password
		public function updatePassword($username, $password): void {

			global $mysql, $hashUtils;

			// generate hash from password
			$password = $hashUtils->genBlowFish($password);

			// update password
			$mysql->insertQuery("UPDATE users SET password = '$password' WHERE username = '$username'");

			// log to mysql
			$mysql->logToMysql("profile-update", "update password for user: $username");
		}

		// update profile image
		public function updateProfileImage($base64Final, $username): void {

			global $mysql;

			// update image in mysql
			$mysql->insertQuery("UPDATE users SET image_base64 = '$base64Final' WHERE username = '$username'");
        
			// log to mysql
			$mysql->logToMysql("profile-update", "user: $username updated avatar image");
		}

		// check if user is owner
		public function isUserOwner(): bool {
			
			// default state output
			$state = false;

			// get user role
			$userRole = $this->getCurrentRole();

			// get user role to lower case
			$userRole = strtolower($userRole);

			// check if user is owner
			if($userRole == "owner") {
				$state = true;
			}

			return $state;
		}

		// get current username form session
		public function getCurrentUsername(): ?string {

			global $mysql;

			// default username value
			$username = null;

			// get user token value
			$user_token = $this->getUserToken();

			// check if user token is not null
			if ($user_token != null) {
				
				// get username
				$username = $mysql->fetchValue("SELECT username FROM users WHERE token = '".$user_token."'", "username");
			}

			return $username;
		}

		// get user role form session
		public function getCurrentRole(): string {

			global $mysql;

			// default role value
			$role = null;

			// check if user token is not null
			if ($this->getUserToken() != null) {

				// return user role
				$role = $mysql->fetchValue("SELECT role FROM users WHERE token = '".$this->getUserToken()."'", "role");
			} else {
				$this->logout();
			}

			return $role;
		}
		
		//Get user token
		public function getUserToken(): ?string {

			global $mysql;

			// default token value
			$token = null;

			// check if user token in session
			if (!empty($_SESSION["userToken"])) {
				
				// get ids where token 
				$ids = $mysql->fetch("SELECT id FROM users WHERE token='".$_SESSION["userToken"]."'");
				
				// check if token exist in users
				if (count($ids) > 0) {
					$token = $_SESSION["userToken"];
				} 
			}

			return $token;
		}

		// get user ip
		public function getUserIPByToken($token): string {

			global $mysql;

			// default user ip
			$user_ip = null;

			// get ids where token 
			$ids = $mysql->fetch("SELECT id FROM users WHERE token='".$_SESSION["userToken"]."'");
				
			// check if token found
			if (count($ids) > 0) {
				
				// get ip by token
				$ip = $mysql->fetchValue("SELECT remote_addr FROM users WHERE token = '".$token."'", "remote_addr");

				// save to user ip
				$user_ip = $ip;
			}

			return $user_ip;
		}

		// auto user login (for cookie login)
		public function autoLogin(): void {
			
			global $mysql, $config, $sessionUtils, $mainUtils, $urlUtils;

			// get user token
			$user_token = $_COOKIE["userToken"];

			// get user ip
			$user_ip = $mainUtils->getRemoteAdress();

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set login identify session
			$sessionUtils->setSession($config->getValue('login-cookie'), $_COOKIE[$config->getValue('login-cookie')]);
 
			// set token session
			$sessionUtils->setSession("userToken", $user_token);

			// log action to mysql
			$mysql->logToMysql("authenticator", "user: ".$this->getCurrentUsername()." success login by login cookie");

			// update user ip
			$mysql->insertQuery("UPDATE users SET remote_addr='$user_ip' WHERE token='$user_token'");

			// refresh page
			$urlUtils->redirect("?admin=dashboard");
		}

		// get user avara base64 code
		public function getUserAvatar(): string {

			global $mysql;

			// default avatar value
			$avatar = null;

			// get user token
			$token = $this->getUserToken();

			// check if user token is not null
			if ($token != null) {

				// get user profile pic (base64 code)
				$avatar = $mysql->fetchValue("SELECT image_base64 FROM users WHERE token = '".$token."'", "image_base64");
			} else {
				$this->logout();
			}

			return $avatar;
		}
	}
?>
