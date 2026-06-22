<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Property-based tests for the Session helper class.
 *
 * Each test validates a universal correctness property from the design document
 * using randomized inputs across 100+ iterations.
 *
 * Tests run in CLI mode; we manipulate the $_SESSION superglobal directly
 * rather than relying on actual PHP session infrastructure.
 */
class SessionPropertyTest extends TestCase
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

    // =========================================================================
    // Property 6: Session set/get round trip
    // Feature: unit-testing-pipeline, Property 6: Session set/get round trip
    // Validates: Requirements 5.1, 5.2
    // =========================================================================

    /**
     * Data provider generating 100 random key-value pairs.
     * Keys: random alphanumeric strings (3-20 chars)
     * Values: mix of strings, integers, arrays, booleans
     */
    public static function randomKeyValueProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $key = 'key_' . bin2hex(random_bytes(4));

            // Generate random value types
            $valueType = random_int(0, 3);
            switch ($valueType) {
                case 0:
                    $value = bin2hex(random_bytes(random_int(1, 20))); // string
                    break;
                case 1:
                    $value = random_int(-1000, 1000); // integer
                    break;
                case 2:
                    $value = ['item_' . random_int(1, 100), random_int(1, 50)]; // array
                    break;
                case 3:
                    $value = (bool) random_int(0, 1); // boolean
                    break;
            }

            $cases["iteration_$i"] = [$key, $value];
        }
        return $cases;
    }

    /**
     * Property 6: For any string key and any value, after calling Session::set(key, value),
     * calling Session::get(key) returns the exact same value, and Session::has(key) returns true.
     *
     * **Validates: Requirements 5.1, 5.2**
     */
    #[DataProvider('randomKeyValueProvider')]
    public function testPropertySessionSetGetRoundTrip(string $key, mixed $value): void
    {
        \Session::set($key, $value);

        $this->assertSame($value, \Session::get($key));
        $this->assertTrue(\Session::has($key));
    }

    // =========================================================================
    // Property 9: Session destroy clears all data
    // Feature: unit-testing-pipeline, Property 9: Session destroy clears all data
    // Validates: Requirements 5.8
    // =========================================================================

    /**
     * Data provider generating 100 random sets of key-value pairs.
     * Each set contains 1-5 random key-value pairs to store in the session.
     */
    public static function randomSessionDataProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $numKeys = random_int(1, 5);
            $data = [];
            for ($k = 0; $k < $numKeys; $k++) {
                $key = 'key_' . bin2hex(random_bytes(3));
                $value = bin2hex(random_bytes(random_int(1, 10)));
                $data[$key] = $value;
            }
            $cases["iteration_$i"] = [$data];
        }
        return $cases;
    }

    /**
     * Property 9: For any set of key-value pairs previously stored via Session::set,
     * after calling Session::destroy(), the $_SESSION superglobal is empty.
     *
     * **Validates: Requirements 5.8**
     */
    #[DataProvider('randomSessionDataProvider')]
    public function testPropertySessionDestroyClearsAllData(array $data): void
    {
        // Store all key-value pairs
        foreach ($data as $key => $value) {
            \Session::set($key, $value);
        }

        // Ensure session is active so session_destroy() works without warnings
        @session_start();

        // Destroy the session
        \Session::destroy();

        // Assert $_SESSION is empty
        $this->assertEmpty($_SESSION);
    }
}
