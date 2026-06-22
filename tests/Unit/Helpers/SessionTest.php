<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Session helper class.
 *
 * Tests run in CLI mode; we manipulate the $_SESSION superglobal directly
 * rather than relying on actual PHP session infrastructure.
 */
class SessionTest extends TestCase
{
    /**
     * Reset $_SESSION before each test to ensure clean state.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    /**
     * Clean up $_SESSION after each test.
     */
    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    #[Test]
    public function set_get_round_trip_returns_same_value(): void
    {
        \Session::set('username', 'doctorjones');

        $this->assertSame('doctorjones', \Session::get('username'));
    }

    #[Test]
    public function set_get_round_trip_with_numeric_value(): void
    {
        \Session::set('user_id', 42);

        $this->assertSame(42, \Session::get('user_id'));
    }

    #[Test]
    public function set_get_round_trip_with_array_value(): void
    {
        $data = ['role' => 'admin', 'permissions' => ['read', 'write']];
        \Session::set('user_data', $data);

        $this->assertSame($data, \Session::get('user_data'));
    }

    #[Test]
    public function has_returns_true_for_existing_key(): void
    {
        \Session::set('email', 'test@example.com');

        $this->assertTrue(\Session::has('email'));
    }

    #[Test]
    public function has_returns_false_for_non_existing_key(): void
    {
        $this->assertFalse(\Session::has('nonexistent_key'));
    }

    #[Test]
    public function get_returns_null_for_non_existing_key(): void
    {
        $result = \Session::get('missing_key');

        $this->assertNull($result);
    }

    #[Test]
    public function get_returns_custom_default_for_non_existing_key(): void
    {
        $result = \Session::get('missing_key', 'default_value');

        $this->assertSame('default_value', $result);
    }

    #[Test]
    public function is_expired_returns_true_when_elapsed_exceeds_1800_seconds(): void
    {
        $_SESSION['last_activity'] = time() - 1801;

        $this->assertTrue(\Session::isExpired());
    }

    #[Test]
    public function is_expired_returns_false_when_elapsed_within_1800_seconds(): void
    {
        $_SESSION['last_activity'] = time() - 1799;

        $this->assertFalse(\Session::isExpired());
    }

    #[Test]
    public function is_expired_returns_false_at_exact_boundary_of_1800_seconds(): void
    {
        $_SESSION['last_activity'] = time() - 1800;

        $this->assertFalse(\Session::isExpired());
    }

    #[Test]
    public function is_expired_returns_false_when_last_activity_is_not_defined(): void
    {
        // $_SESSION is empty — no 'last_activity' key
        $this->assertFalse(\Session::isExpired());
    }

    #[Test]
    public function destroy_clears_all_previously_stored_session_keys(): void
    {
        // Store some values
        \Session::set('user_id', 1);
        \Session::set('username', 'admin');
        \Session::set('role', 'doctor');

        // Start a session so session_destroy() doesn't emit warnings
        @session_start();

        // Destroy the session
        \Session::destroy();

        // Verify all keys are cleared
        $this->assertEmpty($_SESSION);
        $this->assertFalse(\Session::has('user_id'));
        $this->assertFalse(\Session::has('username'));
        $this->assertFalse(\Session::has('role'));
    }
}
