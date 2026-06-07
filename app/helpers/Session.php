<?php

/**
 * Session Helper
 * 
 * Abstracts PHP native session management with support for
 * inactivity timeout (default 30 minutes).
 */
class Session
{
    /**
     * Start the session if not already active.
     * Checks for expiration and auto-destroys if the session has timed out.
     * Updates the last activity timestamp on every call.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the session has expired due to inactivity
        if (self::isExpired()) {
            self::destroy();
            session_start();
        }

        // Update last activity timestamp
        $_SESSION['last_activity'] = time();
    }

    /**
     * Set a session value.
     *
     * @param string $key   The session key
     * @param mixed  $value The value to store
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     *
     * @param string $key     The session key
     * @param mixed  $default Default value if key does not exist
     * @return mixed The session value or default
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key The session key
     * @return bool True if the key exists in the session
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Destroy the session completely.
     * Clears session data, destroys the session, and removes the session cookie.
     */
    public static function destroy(): void
    {
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Check if the session has expired due to inactivity.
     *
     * @param int $timeoutMinutes Maximum allowed inactivity in minutes (default: 30)
     * @return bool True if the session has exceeded the timeout period
     */
    public static function isExpired(int $timeoutMinutes = 30): bool
    {
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }

        $elapsed = time() - $_SESSION['last_activity'];
        return $elapsed > ($timeoutMinutes * 60);
    }
}
