<?php //The session utils class

    class SessionUtils { 


        /*
          * The function for start session if not started
          * Usage like sessionStartedCheckWithStart()
        */
        public function sessionStartedCheckWithStart() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }


        /*
          * The function for set specific session
          * Usage like setSession("name", "value")
          * Input session name and session value
        */
        public function setSession($sessionName, $sessionValue) {
            $this->sessionStartedCheckWithStart();
            $_SESSION[$sessionName] = $sessionValue;
        }


        /*
          * The function for check if session seted
          * Usage like sessionStartedCheckWithStart("name")
          * Input session name
          * Return true or false
        */
        public function checkSessionSet($sessionName) {
            $this->sessionStartedCheckWithStart();
            if (isset($_SESSION[$sessionName])) {
                return true;
            } else {
                return false;
            }
        }


        /*
          * The function for session destroy (Destroy all user sessions)
          * Usage like sessionDestroy()
        */
        public function sessionDestroy() {
            $this->sessionStartedCheckWithStart();
            session_destroy();
        }


        /*
          * The function for print session array
          * Usage like printSession()
        */
        public function printSession() {
            $this->sessionStartedCheckWithStart();
            print_r($_SESSION);
        }
    }

?>
