<?php
/**
 * Router Class - Simple URL Routing
 */

class Router
{
    private array $routes = [];
    private string $basePath = '/mae';

    public function get(string $path, $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }

    public function post(string $path, $handler): self
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }

    public function patch(string $path, $handler): self
    {
        $this->addRoute('PATCH', $path, $handler);
        return $this;
    }

    public function delete(string $path, $handler): self
    {
        $this->addRoute('DELETE', $path, $handler);
        return $this;
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        // Convert route parameters to regex
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Handle PATCH/DELETE via POST with _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Remove base path from URI
        if (str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        // Ensure URI starts with /
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        // Remove trailing slash (except for root)
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
                $this->handleRoute($route['handler'], $params);
                return;
            }
        }

        // 404
        $this->handle404();
    }

    private function handleRoute($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);

            if (!class_exists($controller)) {
                require_once APP_PATH . "/Controllers/$controller.php";
            }

            $instance = new $controller();
            call_user_func_array([$instance, $method], $params);
        }
    }

    private function handle404(): void
    {
        http_response_code(404);
        view('errors/404');
    }
}

// Global router instance
$GLOBALS['router'] = null;

function router(): Router
{
    return $GLOBALS['router'];
}
