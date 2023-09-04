<?php // user functions (login, logout, etc...)

	namespace becwork\managers;

	class UserManager {

		// check if users table not empty
		public function isUserEmpty() {

			global $mysql;

			// default state output
			$state = false;

			// get users data
			$users = $mysql->fetch("SELECT * FROM users");

			// count users
			$usersCount = count($users);

			// check if user is empty
			if ($usersCount < 1) {
				$state = true;
			}

			return $state;
		}
		
		// check if user logged in
		public function isLoggedIn() {

			global $sessionUtils, $config;

			// default state output
			$state = false;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// check if login cookie seted
			if (isset($_SESSION[$config->getValue('loginCookie')])) {
				
				// check if login cookie is valid
				if ($_SESSION[$config->getValue('loginCookie')] == $config->getValue('loginValue')) {
					$state = true; // this is logged in state
				} 
			}

			return $state;
		} 

		// check if user can login with username and password
		public function canLogin($username, $password) {

			global $mysql;

			// default state output
			$state = false;

			// select ID with valid login data
			$loginSelect = $mysql->fetch("SELECT id FROM users WHERE username = '$username' and password = '$password'");		

			// check if user with password exist
			if (count($loginSelect) == 1) {
				$state = true; 
			}

			return $state;
		}

		// unset login cookie
		public function unSetLoginCookies() {

			global $cookieUtils, $config;

			// unset login key cookie
			$cookieUtils->unset_cookie($config->getValue("loginCookie"));

			// unset token
			$cookieUtils->unset_cookie("userToken");			
		}

		// set login cookie
		public function setLoginCookies($token) {

			global $cookieUtils, $config;

			// set username cookie for next auth
			$cookieUtils->cookieSet("userToken", $token, time() + (60*60*24*7*365));

			// set token cookie for next login
			$cookieUtils->cookieSet($config->getValue("loginCookie"), $config->getValue("loginValue"), time() + (60*60*24*7*365));			
		}

		// set anti log cookie
		public function setAntiLogCookie() {

			global $cookieUtils, $config;

			// set antilog cookie
			$cookieUtils->cookieSet($config->getValue("antiLogCookie"), $config->getValue("antiLogValue"), time() + (60*60*24*7*365));			
		}

		// set login session
		public function setLoginSession($token) {

			global $sessionUtils, $config;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set token session
			$sessionUtils->setSession($config->getValue("loginCookie"), $config->getValue("loginValue"));		

			// set token session
			$sessionUtils->setSession("userToken", $token);
		}

		// logout user
		public function logout() {

			global $mysql, $cookieUtils, $config, $sessionUtils, $urlUtils;
			
			// get username
			$username = $this->getCurrentUsername();

			// destroy all sessions
			$sessionUtils->sessionDestroy();

			// unset login key cookie
			$cookieUtils->unset_cookie($config->getValue("loginCookie"));

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
		public function updatePassword($username, $password) {

			global $mysql, $hashUtils;

			// generate hash from password
			$password = $hashUtils->genBlowFish($password);

			// update password
			$mysql->insertQuery("UPDATE users SET password = '$password' WHERE username = '$username'");

			// log to mysql
			$mysql->logToMysql("profile-update", "update password for user: $username");
		}

		// update profile image
		public function updateProfileImage($base64Final, $username) {

			global $mysql;

			// update image in mysql
			$mysql->insertQuery("UPDATE users SET image_base64 = '$base64Final' WHERE username = '$username'");
        
			// log to mysql
			$mysql->logToMysql("profile-update", "user: $username updated avatar image");
		}

		// check if user is owner
		public function isUserOwner() {
			
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
		public function getCurrentUsername() {

			global $mysql;

			// default username value
			$username = null;

			// get user token value
			$userToken = $this->getUserToken();

			// check if user token is not null
			if ($userToken != null) {
				
				// get username
				$username = $mysql->fetchValue("SELECT username FROM users WHERE token = '".$userToken."'", "username");
			}

			return $username;
		}

		// get user role form session
		public function getCurrentRole() {

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
		public function getUserToken() {

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
		public function getUserIPByToken($token) {

			global $mysql;

			// default user ip
			$userIP = null;

			// get ids where token 
			$ids = $mysql->fetch("SELECT id FROM users WHERE token='".$_SESSION["userToken"]."'");
				
			// check if token found
			if (count($ids) > 0) {
				
				// get ip by token
				$ip = $mysql->fetchValue("SELECT remote_addr FROM users WHERE token = '".$token."'", "remote_addr");

				// save to user ip
				$userIP = $ip;
			}

			return $userIP;
		}

		// auto user login (for cookie login)
		public function autoLogin() {
			
			global $mysql, $config, $sessionUtils, $mainUtils, $urlUtils;

			// get user token
			$userToken = $_COOKIE["userToken"];

			// get user ip
			$userIP = $mainUtils->getRemoteAdress();

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set login identify session
			$sessionUtils->setSession($config->getValue('loginCookie'), $_COOKIE[$config->getValue('loginCookie')]);
 
			// set token session
			$sessionUtils->setSession("userToken", $userToken);

			// log action to mysql
			$mysql->logToMysql("authenticator", "user: ".$this->getCurrentUsername()." success login by login cookie");

			// update user ip
			$mysql->insertQuery("UPDATE users SET remote_addr='$userIP' WHERE token='$userToken'");

			// refresh page
			$urlUtils->redirect("?admin=dashboard");
		}

		// get user avara base64 code
		public function getUserAvatar() {

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
