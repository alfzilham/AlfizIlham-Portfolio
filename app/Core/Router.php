<?php
/**
 * Router — Simple URL routing
 */
class Router
{
    private $routes = [];

    /**
     * Register a GET route
     */
    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register a POST route
     */
    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch($uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $uri === '' ? '/' : '/' . $uri;

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            $this->call($this->routes[$method][$uri]);
            return;
        }

        // Check for root
        if ($uri === '/' && isset($this->routes[$method]['/'])) {
            $this->call($this->routes[$method]['/']);
            return;
        }

        // Check for API routes with parameters
        foreach (($this->routes[$method] ?? []) as $route => $handler) {
            $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Add matched params to $_GET
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $_GET[$key] = $value;
                    }
                }
                $this->call($handler);
                return;
            }
        }

        // 404
        http_response_code(404);
        echo '404 — Page Not Found';
    }

    /**
     * Call the handler
     */
    private function call($handler)
    {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $controller = new $controller();
            $controller->$method();
        } elseif (is_callable($handler)) {
            $handler();
        }
    }
}
