<?php

namespace App\Includes;

use App\Includes\Controller;
use App\Includes\Response;

class Route {
    private static array $gets = [];
    private static array $posts = [];
    private static array $puts = [];
    private static array $deletes = [];

    private Request $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    public function target(): Response {
        $methods = $this->request->getMethods();
        $uri = $this->request->getUri();
        $route = [];
        switch ($methods) {

            case 'GET':
                $route = $this->findRoute(self::$gets, $uri);
                break;
            
            case 'POST':
                $route = $this->findRoute(self::$posts, $uri);
                break;

            case 'PUT':
                $route = $this->findRoute(self::$puts, $uri);
                break;

            case 'GET':
                $route = $this->findRoute(self::$deletes, $uri);
                break;
        }

        if (empty($route)) {
            Logger::error("Route: {$uri} is not defiend as {$methods} method.");
            $errResponse = new Response();
            $errResponse->error(404, "Not Found");
            return $errResponse;
        }

        $instance = new $route['controller']($this->request);
        $response = call_user_func([$instance, $route['method']]);
        return $response;
    }

    private function findRoute($routes, $uri): array|null {
        $matchedItem = null;
        $patternMatchItem = null;

        foreach ($routes as $item) {
            if ($item['path'] === $uri) {
                $matchedItem = $item;
                break;
            }

            if ($patternMatchItem === null) {
                $pattern = preg_replace('/\{[^\/]+\}/', '[^/]+', preg_quote($item['path'], '#'));
                if (preg_match('#^' . $pattern . '$#', $uri)) {
                    $patternMatchItem = $item;
                    break;
                }
            }
        }

        $result = $matchedItem !== null ? $matchedItem : $patternMatchItem;
        return $result;
    }

    public static function get(string $path, mixed $controller, string $method, array $middleware = []) {
        self::$gets[] = ['path' => $path, 'controller' => $controller, 'method' => $method, 'middleware' => $middleware];
    }
    public static function post(string $path, mixed $controller, string $method, array $middleware = []) {
        self::$posts[] = ['path' => $path, 'controller' => $controller, 'method' => $method, 'middleware' => $middleware];
    }
    public static function put(string $path, mixed $controller, string $method, array $middleware = []) {
        self::$puts[] = ['path' => $path, 'controller' => $controller, 'method' => $method, 'middleware' => $middleware];
    }
    public static function delete(string $path, mixed $controller, string $method, array $middleware = []) {
        self::$deletes[] = ['path' => $path, 'controller' => $controller, 'method' => $method, 'middleware' => $middleware];
    }

}