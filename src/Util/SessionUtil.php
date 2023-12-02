<?php

namespace App\Util;

/**
 * SessionUtil provides session management functions.
 */
class SessionUtil
{
    /** * @var SecurityUtil */
    private SecurityUtil $securityUtil;

    /**
     * SessionUtil constructor.
     *
     * @param SecurityUtil $securityUtil The SecurityUtil instance.
     */
    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Start a new session if not already started.
     */
    public function startSession(): void 
    {
        if (session_status() == PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            }
        }
    }

    /**
     * Destroy the current session.
     */
    public function destroySession() {
        $this->startSession();
        session_destroy();
    }

    /**
     * Check if a session with the specified name exists.
     *
     * @param string $session_name The name of the session to check.
     *
     * @return bool Whether the session exists.
     */
    public function checkSession(string $session_name): bool {
        $this->startSession();
        return isset($_SESSION[$session_name]);
    }

    /**
     * Set a session value.
     *
     * @param string $session_name The name of the session.
     * @param string $session_value The value to set for the session.
     */
    public function setSession(string $session_name, string $session_value): void 
    {
        $this->startSession();
        $_SESSION[$session_name] = $this->securityUtil->encryptAes($session_value);
    }

    /**
     * Get the decrypted value of a session.
     *
     * @param string $session_name The name of the session.
     *
     * @return string|null The decrypted session value or null if the session doesn't exist.
     */
    public function getSessionValue(string $session_name): ?string 
    {
        $this->startSession();
        return $this->securityUtil->decryptAes($_SESSION[$session_name]);
    }
}
