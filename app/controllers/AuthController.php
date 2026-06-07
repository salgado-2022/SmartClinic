<?php

require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    /**
     * Display the login form.
     * GET /login
     */
    public function loginForm(): void
    {
        $error = null;
        $lockout = null;
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process login credentials.
     * POST /login
     *
     * Flow:
     * 1. Validate input format
     * 2. Check account lockout
     * 3. Find user by email
     * 4. Verify password
     * 5. On success: reset attempts, create session, redirect
     * 6. On failure: increment attempts, possibly lock account, show generic error
     */
    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $error = null;
        $lockout = null;

        // Step 1: Validate credential format
        $validationErrors = Validator::validateCredentials([
            'email' => $email,
            'password' => $password,
        ]);

        if (!empty($validationErrors)) {
            $error = 'Credenciales inválidas. Verifique su correo y contraseña.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Step 2: Check if account is locked
        if (User::isLocked($email)) {
            $lockout = 'Su cuenta ha sido bloqueada temporalmente por múltiples intentos fallidos. Intente nuevamente en 15 minutos.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Step 3: Find user by email
        $user = User::findByEmail($email);

        if (!$user) {
            // User not found — show generic error (never reveal email doesn't exist)
            $error = 'Credenciales inválidas. Verifique su correo y contraseña.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Step 4: Verify password
        if (!User::verifyPassword($password, $user['password_hash'])) {
            // Increment failed attempts
            User::incrementFailedAttempts($email);

            // Check if we've reached the lockout threshold (5 consecutive failures)
            if (User::getFailedAttempts($email) >= 5) {
                User::lockAccount($email);
            }

            // Show generic error (never reveal password is the incorrect field)
            $error = 'Credenciales inválidas. Verifique su correo y contraseña.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Step 5: Successful login — reset failed attempts and create session
        User::resetFailedAttempts($email);
        Session::set('user_id', $user['id']);
        Session::set('last_activity', time());

        header('Location: /');
        exit;
    }

    /**
     * Destroy session and redirect to login.
     * POST /logout
     */
    public function logout(): void
    {
        Session::destroy();

        header('Location: /login');
        exit;
    }
}
