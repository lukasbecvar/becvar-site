<?php // session utils

	namespace becwork\utils;

	class SessionUtils { 

		/*
		  * The function for start session if not started
		  * Usage like sessionStartedCheckWithStart()
		*/
		public function sessionStartedCheckWithStart(): void {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		}

		/*
		  * The function for set specific session
		  * Usage like setSession("name", "value")
		  * Input session name and session value
		*/
		public function setSession($session_name, $session_value): void {
			$this->sessionStartedCheckWithStart();
			$_SESSION[$session_name] = $session_value;
		}

		/*
		  * The function for check if session seted
		  * Usage like sessionStartedCheckWithStart("name")
		  * Input session name
		  * Return true or false
		*/
		public function checkSessionSet($session_name): bool {

			// default state value
			$state = false;

			// start session
			$this->sessionStartedCheckWithStart();

			// check if session found
			if (isset($_SESSION[$session_name])) {
				$state = true;
			}

			return $state;
		}

		/*
		  * The function for session destroy (Destroy all user sessions)
		  * Usage like sessionDestroy()
		*/
		public function sessionDestroy(): void {
			$this->sessionStartedCheckWithStart();
			session_destroy();
		}

		/*
		  * The function for print session array
		  * Usage like printSession()
		*/
		public function printSession(): void {
			$this->sessionStartedCheckWithStart();
			print_r($_SESSION);
		}
	}
?>
