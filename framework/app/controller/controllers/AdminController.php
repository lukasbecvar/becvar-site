<?php // admin functions in one class

	namespace becwork\controllers;

	class AdminController {

		// check if users table not empty
		public function isUserEmpty() {

			global $mysqlUtils;

			// get users data
			$users = $mysqlUtils->fetch("SELECT * FROM users");

			// check if user is empty
			if (count($users) < 1) {
				return true;
			} else {
				return false;
			}
		}
		
		// check if user logged in
		public function isLoggedIn() {

			global $sessionUtils;
			global $pageConfig;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// check if login cookie seted
			if (isset($_SESSION[$pageConfig->getValueByName('loginCookie')])) {
				
				// check if login cookie is valid
				if ($_SESSION[$pageConfig->getValueByName('loginCookie')] == $pageConfig->getValueByName('loginValue')) {
					return true;
				} else {
					return false;
				}

			// check if token is null
			} elseif ($this->getUserToken() != NULL) {
				return false;
			} else {
				return false;
			}
		} 

		// check if user can login with username and password
		public function canLogin($username, $password) {

			global $mysqlUtils;

			// select ID with valid login data
			$loginSelect = $mysqlUtils->fetch("SELECT id FROM users WHERE username = '$username' and password = '$password'");
			
			// check if user with password exist
			if (count($loginSelect) == 1) {
				return true; 
			} else { 
				return false; 
			} 
		}

		// unset login cookie
		public function unSetLoginCookies() {

			global $cookieUtils;
			global $pageConfig;

			// unset login key cookie
			$cookieUtils->unset_cookie($pageConfig->getValueByName("loginCookie"));

			// unset token
			$cookieUtils->unset_cookie("userToken");			
		}

		// set login cookie
		public function setLoginCookies($token) {

			global $cookieUtils;
			global $pageConfig;

			// set username cookie for next auth
			$cookieUtils->cookieSet("userToken", $token, time() + (60*60*24*7*365));

			// set token cookie for next login
			$cookieUtils->cookieSet($pageConfig->getValueByName("loginCookie"), $pageConfig->getValueByName("loginValue"), time() + (60*60*24*7*365));			
		}

		// set anti log cookie
		public function setAntiLogCookie() {

			global $cookieUtils;
			global $pageConfig;

			// set antilog cookie
			$cookieUtils->cookieSet($pageConfig->getValueByName("antiLogCookie"), $pageConfig->getValueByName("antiLogValue"), time() + (60*60*24*7*365));			
		}

		// set login session
		public function setLoginSession($token) {

			global $sessionUtils;
			global $pageConfig;

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set token session
			$sessionUtils->setSession($pageConfig->getValueByName("loginCookie"), $pageConfig->getValueByName("loginValue"));		

			// set token session
			$sessionUtils->setSession("userToken", $token);
		}

		// logout user
		public function logout() {

			// init all classes
			global $cookieUtils;
			global $mysqlUtils;
			global $urlUtils;
			global $sessionUtils;
			global $pageConfig;
			
			// destroy all sessions
			$sessionUtils->sessionDestroy();

			// unset login key cookie
			$cookieUtils->unset_cookie($pageConfig->getValueByName("loginCookie"));

			// unset username
			$cookieUtils->unset_cookie("userToken");

			// log logout to mysql dsatabase 
			if (!empty($this->getCurrentUsername())) {

				// log logout action
				$mysqlUtils->logToMysql("Logout", "User ".$this->getCurrentUsername()." logout out of admin site");
			}

			// redirect to index page
			$urlUtils->redirect("?admin=login");			
		}

		// update password
		public function updatePassword($username, $password) {

			global $mysqlUtils;
			global $hashUtils;

			// generate hash from password
			$password = $hashUtils->genBlowFish($password);

			// update password
			$mysqlUtils->insertQuery("UPDATE users SET password = '$password' WHERE username = '$username'");

			// log to mysql
			$mysqlUtils->logToMysql("Password update", "User $username updated password");
		}

		// update profile image
		public function updateProfileImage($base64Final, $username) {

			global $mysqlUtils;

			// update image in mysql
			$mysqlUtils->insertQuery("UPDATE users SET image_base64 = '$base64Final' WHERE username = '$username'");
        
			// log to mysql
			$mysqlUtils->logToMysql("Profile update", "User $username updated image");
		}

		// check if user is owner
		public function isUserOwner() {
			
			// check if user is owner
			if($this->getCurrentRole() == "Owner" or $this->getCurrentRole() == "owner") {
				return true;
			} else {
				return false;
			}
		}

		// get current username form session
		public function getCurrentUsername() {

			global $mysqlUtils;

			// check if user token is not null
			if ($this->getUserToken() != NULL) {
				
				// return username
				return $mysqlUtils->fetchValue("SELECT username FROM users WHERE token = '".$this->getUserToken()."'", "username");
			} else {
				$this->logout();
			}
		}

		// get user role form session
		public function getCurrentRole() {

			global $mysqlUtils;

			// check if user token is not null
			if ($this->getUserToken() != NULL) {

				// return user role
				return $mysqlUtils->fetchValue("SELECT role FROM users WHERE token = '".$this->getUserToken()."'", "role");
			} else {
				$this->logout();
			}
		}
		
		//Get user token
		public function getUserToken() {

			global $mysqlUtils;

			// check if user token in session
			if (!empty($_SESSION["userToken"])) {
				
				// get ids where token 
				$ids = $mysqlUtils->fetch("SELECT id FROM users WHERE token='".$_SESSION["userToken"]."'");
				
				// check if token exist in users
				if (count($ids) > 0) {
					return $_SESSION["userToken"];
				} else {
					return NULL;
				}

			} else {
				return NULL;
			}
		}

		// get user ip
		public function getUserIPByToken($token) {

			global $mysqlUtils;

			// get ids where token 
			$ids = $mysqlUtils->fetch("SELECT id FROM users WHERE token='".$_SESSION["userToken"]."'");
				
			// check if token found
			if (count($ids) > 0) {
				
				// get ip by token
				$ip = $mysqlUtils->fetchValue("SELECT remote_addr FROM users WHERE token = '".$token."'", "remote_addr");

				return $ip;

			} else {
				return null;
			}
		}

		// auto user login (for cookie login)
		public function autoLogin() {
			
			global $sessionUtils;
			global $mysqlUtils;
			global $urlUtils;
			global $pageConfig;
			global $mainUtils;

			// get user token
			$userToken = $_COOKIE["userToken"];

			// get user ip
			$userIP = $mainUtils->getRemoteAdress();

			// start session
			$sessionUtils->sessionStartedCheckWithStart();

			// set login identify session
			$sessionUtils->setSession($pageConfig->getValueByName('loginCookie'), $_COOKIE[$pageConfig->getValueByName('loginCookie')]);
 
			// set token session
			$sessionUtils->setSession("userToken", $userToken);

			// log action to mysql
			$mysqlUtils->logToMysql("Success login", "user ".$this->getCurrentUsername()." success login by login cookie");

			// update user ip
			$mysqlUtils->insertQuery("UPDATE users SET remote_addr='$userIP' WHERE token='$userToken'");

			// refresh page
			$urlUtils->redirect("?admin=dashboard");
		}

		// get user avara base64 code
		public function getUserAvatar() {

			global $mysqlUtils;

			// check if user token is not null
			if ($this->getUserToken() != NULL) {

				// return user profile pic
				return $mysqlUtils->fetchValue("SELECT image_base64 FROM users WHERE token = '".$this->getUserToken()."'", "image_base64");
			} else {
				$this->logout();
			}
		}
	}
?>
