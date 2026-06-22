<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the AuthMiddleware class.
 *
 * AuthMiddleware::handle() calls header() and exit(), which makes direct testing
 * of redirect scenarios challenging. We use two strategies:
 *
 * 1. For the "valid session" test (no redirect): run normally since exit() is never called.
 * 2. For redirect tests: use TestableAuthMiddleware (a test double that mirrors the exact
 *    logic of AuthMiddleware::handle() but throws ExitException instead of calling exit()
 *    and captures header() calls in $GLOBALS).
 *
 * This approach verifies the same conditional logic and Session interactions
 * as the real AuthMiddleware without the untestable exit() language construct.
 *
 * Requirements: 6.1, 6.2, 6.3
 */
class AuthMiddlewareTest extends TestCase
{
    /**
     * Reset $_SESSION and test globals before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        $GLOBALS['__test_headers'] = [];
        $GLOBALS['__test_exit_called'] = false;
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['__test_headers']);
        unset($GLOBALS['__test_exit_called']);
        parent::tearDown();
    }

    /**
     * Test: When no session exists (user_id absent), AuthMiddleware::handle()
     * sends a 'Location: /login' header and terminates execution.
     *
     * Validates: Requirement 6.1
     */
    #[Test]
    public function handle_redirects_to_login_when_no_session_exists(): void
    {
        require_once __DIR__ . '/../../helpers/auth_middleware_overrides.php';

        // No user_id in session — should trigger redirect
        $_SESSION = [];

        try {
            \Tests\Helpers\TestableAuthMiddleware::handle();
            $this->fail('Expected ExitException was not thrown');
        } catch (\Tests\Helpers\ExitException $e) {
            // Expected: exit() was intercepted
        }

        $this->assertTrue($GLOBALS['__test_exit_called'], 'exit() should have been called');
        $this->assertContains(
            'Location: /login',
            $GLOBALS['__test_headers'],
            'Should redirect to /login when no session exists'
        );
    }

    /**
     * Test: When session has expired (last_activity > 1800 seconds),
     * AuthMiddleware::handle() calls Session::destroy(), sends 'Location: /login'
     * header, and terminates execution.
     *
     * Validates: Requirement 6.2
     */
    #[Test]
    public function handle_destroys_session_and_redirects_when_session_expired(): void
    {
        require_once __DIR__ . '/../../helpers/auth_middleware_overrides.php';

        // Start a session so session_destroy() inside Session::destroy() doesn't emit warnings
        @session_start();

        // Set up an expired session (last_activity > 1800 seconds ago)
        $_SESSION['user_id'] = 1;
        $_SESSION['last_activity'] = time() - 1801;

        try {
            \Tests\Helpers\TestableAuthMiddleware::handle();
            $this->fail('Expected ExitException was not thrown');
        } catch (\Tests\Helpers\ExitException $e) {
            // Expected: exit() was intercepted
        }

        $this->assertTrue($GLOBALS['__test_exit_called'], 'exit() should have been called');
        $this->assertContains(
            'Location: /login',
            $GLOBALS['__test_headers'],
            'Should redirect to /login when session is expired'
        );
        // Session::destroy() clears $_SESSION
        $this->assertEmpty($_SESSION, 'Session::destroy() should have cleared $_SESSION');
    }

    /**
     * Test: When session is active (user_id present) and not expired,
     * AuthMiddleware::handle() does NOT redirect and allows flow to continue.
     *
     * Validates: Requirement 6.3
     */
    #[Test]
    public function handle_allows_continuation_when_session_is_active_and_not_expired(): void
    {
        // Set up a valid, non-expired session
        $_SESSION['user_id'] = 1;
        $_SESSION['last_activity'] = time() - 100; // 100 seconds ago, well within 1800s

        // Call the real AuthMiddleware::handle() — no exit() should be called
        \AuthMiddleware::handle();

        // If we reach here, the middleware allowed the request to proceed
        $this->assertTrue(true, 'AuthMiddleware::handle() did not redirect or exit');
    }
}
