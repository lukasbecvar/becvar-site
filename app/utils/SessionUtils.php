<?php // session utils

	namespace becwork\utils;

	class SessionUtils { 

		/*
		  * The function for start session if not started
		  * Usage like start()
		*/
		public function start(): void {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		}

		/*
		  * The function for set specific session
		  * Usage like set("name", "value")
		  * Input session name and session value
		*/
		public function set($session_name, $session_value): void {
			$this->start();
			$_SESSION[$session_name] = $session_value;
		}

		/*
		  * The function for check if session seted
		  * Usage like start("name")
		  * Input session name
		  * Return true or false
		*/
		public function check($session_name): bool {

			// default state value
			$state = false;

			// start session
			$this->start();

			// check if session found
			if (isset($_SESSION[$session_name])) {
				$state = true;
			}

			return $state;
		}

		/*
		  * The function for session destroy (Destroy all user sessions)
		  * Usage like destroy()
		*/
		public function destroy(): void {
			$this->start();
			session_destroy();
		}
	}
?>