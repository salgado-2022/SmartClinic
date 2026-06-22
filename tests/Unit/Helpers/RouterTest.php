<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

/**
 * Mock controller used to verify Router dispatches to the correct handler.
 * Records all method calls and arguments for assertion.
 */
class MockController
{
    /** @var array<string, array> Records of method calls: [methodName => [args]] */
    public static array $calls = [];

    public static function reset(): void
    {
        self::$calls = [];
    }

    public function index(): void
    {
        self::$calls['index'] = func_get_args();
    }

    public function show(): void
    {
        self::$calls['show'] = func_get_args();
    }

    public function store(): void
    {
        self::$calls['store'] = func_get_args();
    }
}

class RouterTest extends TestCase
{
    private \Router $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new \Router();
        MockController::reset();
        // Reset http_response_code to 200 before each test
        http_response_code(200);
    }

    // =========================================================================
    // Route Registration and Dispatch - Exact Path Match (Requirement 4.1)
    // =========================================================================

    public function testDispatchWithExactPathMatchInvokesCorrectHandler(): void
    {
        $this->router->addRoute('GET', '/patients', [
            MockController::class,
            'index',
        ]);

        $this->router->dispatch('GET', '/patients');

        $this->assertArrayHasKey('index', MockController::$calls,
            'Expected the index method to be called on the controller');
    }

    public function testDispatchMatchesMethodCaseInsensitively(): void
    {
        $this->router->addRoute('get', '/doctors', [
            MockController::class,
            'index',
        ]);

        $this->router->dispatch('GET', '/doctors');

        $this->assertArrayHasKey('index', MockController::$calls,
            'Expected Router to match route regardless of HTTP method case');
    }

    public function testDispatchTrimsTrailingSlashFromUri(): void
    {
        $this->router->addRoute('GET', '/patients', [
            MockController::class,
            'index',
        ]);

        $this->router->dispatch('GET', '/patients/');

        $this->assertArrayHasKey('index', MockController::$calls,
            'Expected Router to match route when URI has trailing slash');
    }

    // =========================================================================
    // 404 Response for Unmatched URI (Requirement 4.2)
    // =========================================================================

    public function testDispatchSets404ForUnmatchedUri(): void
    {
        $this->router->addRoute('GET', '/patients', [
            MockController::class,
            'index',
        ]);

        // Buffer output since the 404 view will produce HTML
        ob_start();
        $this->router->dispatch('GET', '/nonexistent');
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            'Expected 404 response code for unmatched URI');
        $this->assertEmpty(MockController::$calls,
            'Expected no controller methods to be called for unmatched URI');
    }

    public function testDispatchSets404WhenNoRoutesRegistered(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/anything');
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            'Expected 404 response code when no routes are registered');
    }

    // =========================================================================
    // 404 Response for Correct URI but Wrong HTTP Method (Requirement 4.4)
    // =========================================================================

    public function testDispatchSets404ForCorrectUriButWrongMethod(): void
    {
        $this->router->addRoute('POST', '/patients', [
            MockController::class,
            'store',
        ]);

        // Buffer output since the 404 view will produce HTML
        ob_start();
        $this->router->dispatch('GET', '/patients');
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            'Expected 404 response code when URI matches but HTTP method does not');
        $this->assertEmpty(MockController::$calls,
            'Expected no controller methods to be called when HTTP method does not match');
    }

    public function testDispatchSets404ForPostWhenOnlyGetRegistered(): void
    {
        $this->router->addRoute('GET', '/doctors', [
            MockController::class,
            'index',
        ]);

        ob_start();
        $this->router->dispatch('POST', '/doctors');
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            'Expected 404 when POST is sent to a GET-only route');
        $this->assertEmpty(MockController::$calls,
            'Expected no controller methods to be called for wrong HTTP method');
    }

    // =========================================================================
    // Dynamic Parameter Extraction (Requirement 4.3)
    // =========================================================================

    public function testDispatchExtractsSingleDynamicParameter(): void
    {
        $this->router->addRoute('GET', '/doctors/{id}', [
            MockController::class,
            'show',
        ]);

        $this->router->dispatch('GET', '/doctors/42');

        $this->assertArrayHasKey('show', MockController::$calls,
            'Expected the show method to be called on the controller');
        $this->assertEquals(['42'], MockController::$calls['show'],
            'Expected dynamic parameter "42" to be passed as argument to show method');
    }

    public function testDispatchExtractsMultipleDynamicParameters(): void
    {
        $this->router->addRoute('GET', '/patients/{patientId}/appointments/{appointmentId}', [
            MockController::class,
            'show',
        ]);

        $this->router->dispatch('GET', '/patients/7/appointments/123');

        $this->assertArrayHasKey('show', MockController::$calls,
            'Expected the show method to be called on the controller');
        $this->assertEquals(['7', '123'], MockController::$calls['show'],
            'Expected both dynamic parameters "7" and "123" to be passed as arguments in order');
    }

    public function testDispatchExtractsDynamicParameterWithStringValue(): void
    {
        $this->router->addRoute('GET', '/specialties/{name}', [
            MockController::class,
            'show',
        ]);

        $this->router->dispatch('GET', '/specialties/cardiology');

        $this->assertArrayHasKey('show', MockController::$calls,
            'Expected the show method to be called on the controller');
        $this->assertEquals(['cardiology'], MockController::$calls['show'],
            'Expected string parameter "cardiology" to be passed as argument to show method');
    }
}
