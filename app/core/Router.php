<?php
/**
 * Router – maps URI + HTTP method to a controller action.
 *
 * Usage (in public/index.php):
 *
 *   Router::get('/',               'HomeController@index');
 *   Router::get('/movies',         'MoviesController@index');
 *   Router::post('/bookings',      'BookingController@store');
 *   Router::get('/movies/{id}',    'MoviesController@show');
 *
 *   Router::dispatch();
 */

class Router
{
    private static array $routes = [];

    // ── Route registration ──────────────────────────────────────────

    public static function get(string $uri, string $action): void
    {
        self::addRoute('GET', $uri, $action);
    }

    public static function post(string $uri, string $action): void
    {
        self::addRoute('POST', $uri, $action);
    }

    public static function put(string $uri, string $action): void
    {
        self::addRoute('PUT', $uri, $action);
    }

    public static function delete(string $uri, string $action): void
    {
        self::addRoute('DELETE', $uri, $action);
    }

    private static function addRoute(string $method, string $uri, string $action): void
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'uri'    => rtrim($uri, '/') ?: '/',
            'action' => $action,
        ];
    }

    // ── Dispatch ────────────────────────────────────────────────────

    public static function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Support method override via hidden _method input (PUT / DELETE from forms).
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach (self::$routes as $route) {
            $params = self::match($route['uri'], $uri);

            if ($params !== false && $route['method'] === $method) {
                self::invoke($route['action'], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        View::render('errors/404', [], 'layouts/main');
    }

    /**
     * Try to match $routeUri (with {placeholders}) against $requestUri.
     *
     * @return array|false  Associative array of captured params, or false on mismatch.
     */
    private static function match(string $routeUri, string $requestUri): array|false
    {
        // Convert {param} → named capture group.
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            // Keep only named captures.
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Instantiate the controller and call the action method.
     */
    private static function invoke(string $action, array $params): void
    {
        [$class, $method] = explode('@', $action, 2);

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: {$class}");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$class}::{$method}() not found.");
        }

        call_user_func_array([$controller, $method], array_values($params));
    }
}
