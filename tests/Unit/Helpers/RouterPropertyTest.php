<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Mock controller for Router property tests.
 * Records all method calls and arguments for assertion.
 */
class PropertyMockController
{
    public static array $calls = [];

    public static function reset(): void
    {
        self::$calls = [];
    }

    public function handle(): void
    {
        self::$calls['handle'] = func_get_args();
    }
}

/**
 * Property-based tests for the Router class.
 *
 * Feature: unit-testing-pipeline, Property 3: Router dispatches when method and path match
 * Feature: unit-testing-pipeline, Property 5: Router extracts dynamic parameters correctly
 */
class RouterPropertyTest extends TestCase
{
    /**
     * Generate a random path segment of 3-10 lowercase letters.
     */
    private static function generateRandomSegment(): string
    {
        $length = rand(3, 10);
        $segment = '';
        for ($i = 0; $i < $length; $i++) {
            $segment .= chr(rand(ord('a'), ord('z')));
        }
        return $segment;
    }

    /**
     * Generate a random path with 1-3 segments.
     * Example outputs: /abc, /patients/list, /api/v1/data
     */
    private static function generateRandomPath(): string
    {
        $numSegments = rand(1, 3);
        $segments = [];
        for ($i = 0; $i < $numSegments; $i++) {
            $segments[] = self::generateRandomSegment();
        }
        return '/' . implode('/', $segments);
    }

    /**
     * Data provider that generates 100 random matching routes.
     *
     * **Validates: Requirements 4.1, 4.4**
     */
    public static function randomMatchingRouteProvider(): array
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            $method = $methods[array_rand($methods)];
            $path = self::generateRandomPath();
            $cases["route_iteration_$i"] = [$method, $path];
        }
        return $cases;
    }

    /**
     * Property 3: Router dispatches when method and path match.
     *
     * For any set of registered routes, when a request is dispatched with an HTTP method
     * and URI that exactly match a registered route, the Router SHALL invoke the handler
     * associated with that route.
     *
     * **Validates: Requirements 4.1, 4.4**
     */
    #[DataProvider('randomMatchingRouteProvider')]
    public function testPropertyRouterDispatchesOnMatch(string $method, string $path): void
    {
        $router = new \Router();
        PropertyMockController::reset();

        $router->addRoute($method, $path, [PropertyMockController::class, 'handle']);
        $router->dispatch($method, $path);

        $this->assertArrayHasKey('handle', PropertyMockController::$calls,
            "Expected handler to be called for $method $path");
    }

    /**
     * Generate a random alphabetic string of given length range.
     */
    private static function randomAlpha(int $minLen, int $maxLen): string
    {
        $length = rand($minLen, $maxLen);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= chr(rand(ord('a'), ord('z')));
        }
        return $str;
    }

    /**
     * Generate a random alphanumeric string of given length range (no slashes).
     */
    private static function randomAlphanumeric(int $minLen, int $maxLen): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = rand($minLen, $maxLen);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * Data provider that generates 100 random routes with dynamic parameters.
     *
     * Each case includes a route pattern with {param} placeholders, a matching URI
     * with concrete values, and the expected parameter values in order.
     *
     * **Validates: Requirements 4.3**
     */
    public static function randomDynamicRouteProvider(): array
    {
        $cases = [];
        $paramNames = ['id', 'slug', 'name', 'key', 'code', 'ref', 'tag', 'cat', 'type', 'item'];
        for ($i = 0; $i < 100; $i++) {
            // Generate 1-3 dynamic params
            $numParams = rand(1, 3);
            $routePattern = '';
            $uri = '';
            $expectedValues = [];

            for ($p = 0; $p < $numParams; $p++) {
                $staticPart = '/' . self::randomAlpha(3, 8);
                $paramName = $paramNames[$p];
                $paramValue = self::randomAlphanumeric(2, 10);

                $routePattern .= $staticPart . '/{' . $paramName . '}';
                $uri .= $staticPart . '/' . $paramValue;
                $expectedValues[] = $paramValue;
            }

            $cases["dynamic_params_$i"] = [$routePattern, $uri, $expectedValues];
        }
        return $cases;
    }

    /**
     * Property 5: Router extracts dynamic parameters correctly.
     *
     * For any route registered with {param} placeholders and any URI that matches
     * that pattern, the Router SHALL extract parameter values from the URI segments
     * and pass them as arguments to the handler, preserving the exact string values
     * from the URI.
     *
     * **Validates: Requirements 4.3**
     */
    #[DataProvider('randomDynamicRouteProvider')]
    public function testPropertyRouterExtractsDynamicParametersCorrectly(string $routePattern, string $uri, array $expectedValues): void
    {
        $router = new \Router();
        PropertyMockController::reset();

        $router->addRoute('GET', $routePattern, [PropertyMockController::class, 'handle']);
        $router->dispatch('GET', $uri);

        $this->assertArrayHasKey('handle', PropertyMockController::$calls,
            "Expected handler to be called for route pattern '$routePattern' with URI '$uri'");
        $this->assertEquals($expectedValues, PropertyMockController::$calls['handle'],
            "Expected extracted parameters to match URI segments for pattern '$routePattern'");
    }

    // =========================================================================
    // Property 4: Router returns 404 for unmatched requests
    // =========================================================================

    /**
     * Data provider that generates 100 random URIs guaranteed not to match
     * any registered route.
     *
     * **Validates: Requirements 4.2, 4.4**
     *
     * @return array<string, array{0: string}>
     */
    public static function randomUnmatchedUriProvider(): array
    {
        $cases = [];
        for ($i = 0; $i < 100; $i++) {
            // Generate a URI guaranteed not to match registered routes
            // Uses /nonexistent_ prefix + random hex to ensure no collision
            $uri = '/nonexistent_' . bin2hex(random_bytes(4));
            $cases["unmatched_uri_$i"] = [$uri];
        }
        return $cases;
    }

    /**
     * Property 4: Router returns 404 for unmatched requests.
     *
     * For any set of registered routes and any request URI that does not match
     * any registered route path, the Router SHALL set HTTP response code 404.
     *
     * Feature: unit-testing-pipeline, Property 4: Router returns 404 for unmatched requests
     *
     * **Validates: Requirements 4.2, 4.4**
     */
    #[DataProvider('randomUnmatchedUriProvider')]
    public function testPropertyRouterReturns404ForUnmatchedRequests(string $uri): void
    {
        $router = new \Router();
        // Register some routes that won't match
        $router->addRoute('GET', '/patients', [PropertyMockController::class, 'handle']);
        $router->addRoute('POST', '/doctors', [PropertyMockController::class, 'handle']);
        $router->addRoute('PUT', '/appointments/{id}', [PropertyMockController::class, 'handle']);

        PropertyMockController::reset();
        http_response_code(200); // Reset to default

        ob_start();
        $router->dispatch('GET', $uri);
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            "Expected 404 response code for unmatched URI: $uri");
        $this->assertEmpty(PropertyMockController::$calls,
            "Expected no controller methods to be called for unmatched URI: $uri");
    }

    /**
     * Data provider that generates 50 cases where a route is registered with
     * one HTTP method but the request uses a different method on the same path.
     *
     * **Validates: Requirements 4.2, 4.4**
     *
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function randomWrongMethodProvider(): array
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE'];
        $cases = [];
        for ($i = 0; $i < 50; $i++) {
            $registeredMethod = $methods[array_rand($methods)];
            // Pick a different method for the request
            do {
                $requestMethod = $methods[array_rand($methods)];
            } while ($requestMethod === $registeredMethod);
            $path = '/route_' . bin2hex(random_bytes(3));
            $cases["wrong_method_$i"] = [$registeredMethod, $requestMethod, $path];
        }
        return $cases;
    }

    /**
     * Property 4: Router returns 404 when path matches but HTTP method does not.
     *
     * For any registered route, when the request uses the same path but a different
     * HTTP method, the Router SHALL set HTTP response code 404.
     *
     * Feature: unit-testing-pipeline, Property 4: Router returns 404 for unmatched requests
     *
     * **Validates: Requirements 4.2, 4.4**
     */
    #[DataProvider('randomWrongMethodProvider')]
    public function testPropertyRouterReturns404ForWrongMethod(
        string $registeredMethod,
        string $requestMethod,
        string $path
    ): void {
        $router = new \Router();
        $router->addRoute($registeredMethod, $path, [PropertyMockController::class, 'handle']);

        PropertyMockController::reset();
        http_response_code(200); // Reset to default

        ob_start();
        $router->dispatch($requestMethod, $path);
        ob_end_clean();

        $this->assertEquals(404, http_response_code(),
            "Expected 404 when $requestMethod is sent to a route registered as $registeredMethod $path");
        $this->assertEmpty(PropertyMockController::$calls,
            "Expected no controller methods to be called when HTTP method does not match");
    }
}
