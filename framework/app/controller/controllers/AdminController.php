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
			} else {
				return false;
			}
		} 



		//if check if user can login with username and password
		public function canLogin($username, $password) {

			global $mysqlUtils;
			global $pageConfig;

			$result = mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT id FROM users WHERE username = '$username' and password = '$password'"); 
			
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC); 
			
			$count = mysqli_num_rows($result);
			
			if ($count == 1) {
				return true; 
			} else { 
				return false; 
			} 
		}



		//Unset login cookie
		public function unSetLoginCookies($username) {

			global $cookieUtils;
			global $pageConfig;

			//Unset login key cookie
			$cookieUtils->unset_cookie($pageConfig->getValueByName("loginCookie"));

			//Unset username
			$cookieUtils->unset_cookie($username);			
		}



		//Set login cookie
		public function setLoginCookies($username) {

			global $cookieUtils;
			global $pageConfig;

			//Set username cookie for next auth
			$cookieUtils->cookieSet("username", $username, time() + (60*60*24*7*365));

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
		public function setLoginSession($username) {

			global $sessionUtils;
			global $pageConfig;

			//Start session
			$sessionUtils->sessionStartedCheckWithStart();

			//Set token session
			$sessionUtils->setSession($pageConfig->getValueByName("loginCookie"), $pageConfig->getValueByName("loginValue"));		

			//Set username session
			$sessionUtils->setSession("username", $username);
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
			$cookieUtils->unset_cookie("username");

			//Log action to mysql dsatabase 
			if (!empty($_SESSION["username"])) {
				$mysqlUtils->logToMysql("Logout", "User ".$_SESSION["username"]." logout out of admin site");
			}

			//Redirect to index page
			$urlUtils->redirect("index.php?page=admin");			
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
			if($_SESSION["role"] == "Owner") {
				return true;
			} else {
				return false;
			}
		}



		//Get current username form session
		public function getCurrentUsername() {
			if (isset($_SESSION["username"])) {
				return $_SESSION["username"];
			} else {
				return null;
			}
		}



		//Get user role form session
		public function getCurrentRole() {
			if (isset($_SESSION["role"])) {
				return $_SESSION["role"];
			} else {
				return null;
			}
		}



		//Auto user login (for cookie login)
		public function autoLogin() {
			
			global $sessionUtils;
			global $mysqlUtils;
			global $urlUtils;
			global $pageConfig;

			//Start session
			$sessionUtils->sessionStartedCheckWithStart();

			//Set token session
			$sessionUtils->setSession($pageConfig->getValueByName('loginCookie'), $_COOKIE[$pageConfig->getValueByName('loginCookie')]);

			//Set username session
			$sessionUtils->setSession("username", $_COOKIE["username"]);

			//Set role session
			$sessionUtils->setSession("role", $mysqlUtils->readFromMysql("SELECT role FROM users WHERE username = '".$_SESSION["username"]."'", "role"));

			//log action to mysql
			$mysqlUtils->logToMysql("Success login", "user ".$_COOKIE["username"]." success login by login cookie");

			//Refresh page
			$urlUtils->redirect("index.php?page=admin");
		}



		//Get user avara base64 code
		public function getUserAvatar() {

			global $mysqlUtils;

			return $mysqlUtils->readFromMysql("SELECT image_base64 FROM users WHERE username = '".$_SESSION["username"]."'", "image_base64");
		}
	}
?>
