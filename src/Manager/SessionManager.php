<?php

namespace App\Manager;

/*
    Session manager provides session managment
*/

class SessionManager
{

    public function startSession(): void 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function destroySession() {
        $this->startSession();
        session_destroy();
    }

    public function checkSession(string $session_name): bool {
        $this->startSession();
        if (isset($_SESSION[$session_name])) {
            return true;
        } else {
            return false;
        }
    }

    public function setSession(string $session_name, string $session_value): void 
    {
        $this->startSession();
        $_SESSION[$session_name] = $session_value;
    }

    public function getSessionValue(string $session_name): ?string 
    {
        $this->startSession();
        return $_SESSION[$session_name];
    }
}
