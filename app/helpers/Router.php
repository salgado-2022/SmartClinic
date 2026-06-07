<?php
/**
 * Router Class
 * 
 * Handles HTTP request routing by matching URIs to registered routes
 * and dispatching to the appropriate controller action.
 */
class Router
{
    /**
     * @var array Registered routes grouped by HTTP method
     */
    private array $routes = [];

    /**
     * Register a new route.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path   URI pattern (supports {param} placeholders)
     * @param array  $handler Controller and action [ControllerClass, method]
     */
    public function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch the current request to the matching route handler.
     *
     * @param string $method HTTP method of the request
     * @param string $uri    Request URI path
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                // Filter out numeric keys, keep only named parameters
                $params = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                $this->callAction($route['handler'], $params);
                return;
            }
        }

        // No route matched — show 404
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }

    /**
     * Convert a route path pattern to a regex.
     * Transforms {param} placeholders into named capture groups.
     *
     * @param string $path Route path pattern
     * @return string Regex pattern
     */
    private function convertToRegex(string $path): string
    {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);

        // Replace {param} with named capture groups (match word characters and hyphens)
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^\/]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    /**
     * Instantiate the controller and call the action method with parameters.
     *
     * @param array $handler Controller class and method [ControllerClass, method]
     * @param array $params  Route parameters extracted from the URI
     */
    private function callAction(array $handler, array $params): void
    {
        [$controllerClass, $actionMethod] = $handler;

        $controller = new $controllerClass();
        call_user_func_array([$controller, $actionMethod], array_values($params));
    }
}
