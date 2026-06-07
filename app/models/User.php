<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|null User record or null if not found
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Verify a plaintext password against a stored hash.
     *
     * @param string $input The plaintext password input
     * @param string $hash  The stored password hash
     * @return bool True if the password matches
     */
    public static function verifyPassword(string $input, string $hash): bool
    {
        return password_verify($input, $hash);
    }

    /**
     * Get the number of failed login attempts for an email.
     *
     * @param string $email
     * @return int Number of failed attempts
     */
    public static function getFailedAttempts(string $email): int
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT failed_attempts FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        return $result ? (int) $result['failed_attempts'] : 0;
    }

    /**
     * Increment the failed login attempts counter for an email.
     *
     * @param string $email
     * @return void
     */
    public static function incrementFailedAttempts(string $email): void
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('UPDATE users SET failed_attempts = failed_attempts + 1 WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }

    /**
     * Reset the failed login attempts counter and unlock the account.
     *
     * @param string $email
     * @return void
     */
    public static function resetFailedAttempts(string $email): void
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }

    /**
     * Check if an account is currently locked.
     *
     * @param string $email
     * @return bool True if the account is locked (locked_until > now)
     */
    public static function isLocked(string $email): bool
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT locked_until FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        if (!$result || $result['locked_until'] === null) {
            return false;
        }

        return strtotime($result['locked_until']) > time();
    }

    /**
     * Lock an account for 15 minutes from the current time.
     *
     * @param string $email
     * @return void
     */
    public static function lockAccount(string $email): void
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }
}
