<?php //All admin functions in one class

	class AdminController {

		//Check if users table not empty
		public function isUserEmpty() {

			global $mysqlUtils;
			global $pageConfig;

			$userCount = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM users"));

			if ($userCount["count"] == 0) {
				return true;
			} else {
				return false;
			}
		}
		
		//if check if user logged in
		public function isLoggedIn() {

			global $sessionUtils;
			global $pageConfig;

			$sessionUtils->sessionStartedCheckWithStart();

			if (isset($_SESSION[$pageConfig->getValueByName('loginCookie')])) {
				if ($_SESSION[$pageConfig->getValueByName('loginCookie')] == $pageConfig->getValueByName('loginValue')) {
					return true;
				} else {
					return false;
				}
			} elseif ($this->getUserToken() != NULL) {
				return false;
			} else {
				return false;
			}
		} 

		//if check if user can login with username and password
		public function canLogin($username, $password) {

			global $mysqlUtils;
			global $pageConfig;

			$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT id FROM users WHERE username = '$username' and password = '$password'"); 
						
			$count = mysqli_num_rows($result);
			
			if ($count == 1) {
				return true; 
			} else { 
				return false; 
			} 
		}

		//Unset login cookie
		public function unSetLoginCookies() {

			global $cookieUtils;
			global $pageConfig;

			//Unset login key cookie
			$cookieUtils->unset_cookie($pageConfig->getValueByName("loginCookie"));

			//Unset token
			$cookieUtils->unset_cookie("userToken");			
		}

		//Set login cookie
		public function setLoginCookies($token) {

			global $cookieUtils;
			global $pageConfig;

			//Set username cookie for next auth
			$cookieUtils->cookieSet("userToken", $token, time() + (60*60*24*7*365));

			//Set token cookie for next login
			$cookieUtils->cookieSet($pageConfig->getValueByName("loginCookie"), $pageConfig->getValueByName("loginValue"), time() + (60*60*24*7*365));			
		}

		//Set anti log cookie
		public function setAntiLogCookie() {

			global $cookieUtils;
			global $pageConfig;

			//Set antilog cookie
			$cookieUtils->cookieSet($pageConfig->getValueByName("antiLogCookie"), $pageConfig->getValueByName("antiLogValue"), time() + (60*60*24*7*365));			
		}

		//Set login session
		public function setLoginSession($token) {

			global $sessionUtils;
			global $pageConfig;

			//Start session
			$sessionUtils->sessionStartedCheckWithStart();

			//Set token session
			$sessionUtils->setSession($pageConfig->getValueByName("loginCookie"), $pageConfig->getValueByName("loginValue"));		

			//Set token session
			$sessionUtils->setSession("userToken", $token);
		}

		//Logout user
		public function logout() {

			//init all classes
			global $cookieUtils;
			global $mysqlUtils;
			global $urlUtils;
			global $sessionUtils;
			global $pageConfig;
			
			//Destroy all sessions
			$sessionUtils->sessionDestroy();

			//Unset login key cookie
			$cookieUtils->unset_cookie($pageConfig->getValueByName("loginCookie"));

			//Unset username
			$cookieUtils->unset_cookie("userToken");

			//Log action to mysql dsatabase 
			if (!empty($this->getCurrentUsername())) {

				$mysqlUtils->logToMysql("Logout", "User ".$this->getCurrentUsername()." logout out of admin site");
			}

			//Redirect to index page
			$urlUtils->redirect("?admin=login");			
		}

		//Update password
		public function updatePassword($username, $password) {

			global $mysqlUtils;
			global $hashUtils;

			//Generate hash from password
			$password = $hashUtils->genBlowFish($password);

			//Update password
			$mysqlUtils->insertQuery("UPDATE users SET password = '$password' WHERE username = '$username'");

			//Log to mysql
			$mysqlUtils->logToMysql("Password update", "User $username updated password");
		}

		//Update profile image
		public function updateProfileImage($base64Final, $username) {

			global $mysqlUtils;

			//Update image in mysql
			$mysqlUtils->insertQuery("UPDATE users SET image_base64 = '$base64Final' WHERE username = '$username'");
        
			//Log to mysql
			$mysqlUtils->logToMysql("Profile update", "User $username updated image");
		}

		//Check if user is owner
		public function isUserOwner() {
			if($this->getCurrentRole() == "Owner" or $this->getCurrentRole() == "owner") {
				return true;
			} else {
				return false;
			}
		}

		//Get current username form session
		public function getCurrentUsername() {

			global $mysqlUtils;

			if ($this->getUserToken() != NULL) {
				return $mysqlUtils->readFromMysql("SELECT username FROM users WHERE token = '".$this->getUserToken()."'", "username");
			} else {
				return null;
			}
		}

		//Get user role form session
		public function getCurrentRole() {

			global $mysqlUtils;
			global $pageConfig;

			if ($this->getUserToken() != NULL) {
				return $mysqlUtils->readFromMysql("SELECT role FROM users WHERE token = '".$this->getUserToken()."'", "role");
			} else {
				return null;
			}
		}
		
		//Get user token
		public function getUserToken() {

			global $mysqlUtils;
			global $pageConfig;

			if (!empty($_SESSION["userToken"])) {
				
				//Get token count
				$count = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT COUNT(*) AS count FROM users WHERE token='".$_SESSION["userToken"]."'"))["count"];
				
				//Check if token exist in users
				if ($count == "1") {
					return $_SESSION["userToken"];
				} else {
					return NULL;
				}



			} else {
				return NULL;
			}
		}

		//Auto user login (for cookie login)
		public function autoLogin() {
			
			global $sessionUtils;
			global $mysqlUtils;
			global $urlUtils;
			global $pageConfig;
			global $mainUtils;

			//Get user token
			$userToken = $_COOKIE["userToken"];

			//Get user ip
			$userIP = $mainUtils->getRemoteAdress();

			//Start session
			$sessionUtils->sessionStartedCheckWithStart();

			//Set login identify session
			$sessionUtils->setSession($pageConfig->getValueByName('loginCookie'), $_COOKIE[$pageConfig->getValueByName('loginCookie')]);
 
			//Set token session
			$sessionUtils->setSession("userToken", $userToken);

			//log action to mysql
			$mysqlUtils->logToMysql("Success login", "user ".$this->getCurrentUsername()." success login by login cookie");

			//Update user ip
			$mysqlUtils->insertQuery("UPDATE users SET remote_addr='$userIP' WHERE token='$userToken'");

			//Refresh page
			$urlUtils->redirect("?admin=dashboard");
		}

		//Get user avara base64 code
		public function getUserAvatar() {

			global $mysqlUtils;

			if ($this->getUserToken() != NULL) {
				return $mysqlUtils->readFromMysql("SELECT image_base64 FROM users WHERE token = '".$this->getUserToken()."'", "image_base64");
			} else {
				$this->logout();
			}

		}
	}
?>
