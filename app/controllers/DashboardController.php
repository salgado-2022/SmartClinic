<?php

/**
 * Dashboard Controller
 * 
 * Handles the root route (GET /) by redirecting to the appropriate
 * page based on authentication status.
 */
class DashboardController
{
    /**
     * Handle the root URL.
     * Redirects authenticated users to /patients, unauthenticated users to /login.
     */
    public function index(): void
    {
        if (Session::has('user_id')) {
            header('Location: /patients');
        } else {
            header('Location: /login');
        }
        exit;
    }
}
