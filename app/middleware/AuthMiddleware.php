<?php

/**
 * Authentication Middleware
 * 
 * Protects routes that require an active user session.
 * Redirects to /login if the user is not authenticated or
 * if the session has expired due to inactivity (30 minutes).
 */
class AuthMiddleware
{
    /**
     * Check for an active, non-expired session.
     * Redirects to /login if no session exists or if it has timed out.
     *
     * @return void
     */
    public static function handle(): void
    {
        // Check if the session has expired (30-minute inactivity timeout)
        if (Session::isExpired()) {
            Session::destroy();
            header('Location: /login');
            exit();
        }

        // Check if the user is authenticated
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit();
        }
    }
}
