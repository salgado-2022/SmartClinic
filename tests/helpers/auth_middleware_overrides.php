<?php

/**
 * Function overrides for testing AuthMiddleware.
 *
 * This file MUST be included BEFORE AuthMiddleware.php in separate process tests.
 * It provides an ExitException and a TestableAuthMiddleware class that replicates
 * the exact logic of AuthMiddleware::handle() but replaces header()/exit() with
 * interceptable behavior.
 *
 * Strategy: Since we cannot redefine built-in header() or the language construct exit(),
 * we provide a TestableAuthMiddleware that mirrors the production logic exactly but
 * throws an ExitException instead of calling exit() and captures header() calls.
 */

namespace Tests\Helpers;

/**
 * Exception thrown to simulate exit() calls in tests.
 */
class ExitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('exit() was called');
    }
}

/**
 * Testable version of AuthMiddleware that captures header() and throws
 * ExitException instead of calling exit().
 *
 * Mirrors the exact logic of AuthMiddleware::handle().
 */
class TestableAuthMiddleware
{
    /**
     * Check for an active, non-expired session.
     * Same logic as AuthMiddleware::handle() but throws ExitException instead of exit().
     *
     * @throws ExitException when the original would call exit()
     */
    public static function handle(): void
    {
        // Check if the session has expired (30-minute inactivity timeout)
        if (\Session::isExpired()) {
            \Session::destroy();
            $GLOBALS['__test_headers'][] = 'Location: /login';
            $GLOBALS['__test_exit_called'] = true;
            throw new ExitException();
        }

        // Check if the user is authenticated
        if (!\Session::has('user_id')) {
            $GLOBALS['__test_headers'][] = 'Location: /login';
            $GLOBALS['__test_exit_called'] = true;
            throw new ExitException();
        }
    }
}
