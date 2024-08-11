<?php

namespace App\Includes;

use App\Includes\Param;
use App\Includes\Config;
/**
 * HTTP request
 */
class Request {
    private $server;
    private $request;
    private $query;
    private $files;
    private $cookies;
    private $headers;
    
    public function __construct() {

        $this->server = new Param($_SERVER);
        $this->query = new Param($_GET);
        $this->request = new Param($_POST);
        $this->files = new Param($_FILES);
        $this->cookies = new Param($_COOKIE);

        $headers = array();
        foreach ($this->server->all() as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headerName = substr($name, 5);
                $headerName = str_replace('_', ' ', $headerName);
                $headerName = ucwords(strtolower($headerName));
                $headerName = str_replace(' ', '-', $headerName);
                $headers[$headerName] = $value;
            }
        }
        $this->headers = new Param($headers);
    }

    public function getPath(): string {
        $protocol = (!empty($this->server->get('HTTPS')) && $this->server->get('HTTPS') !== 'off' || $this->server->get('SERVER_PORT') == 443) ? "https://" : "http://";
        $host = $this->server->get('HTTP_HOST');
        $requestUri = $this->server->get('REQUEST_URI');
        $fullUrl = $protocol . $host . $requestUri;
        return $fullUrl;
    }

    public function getUri(): string {
        $requestedUri = $this->server->get('REQUEST_URI');
        $baseUrl = Config::baseUrl();
        $relativePath = substr($requestedUri, strlen($baseUrl));
        $relativePath = strtok($relativePath, '?');
        return $relativePath;
    }

    public function getHomePath(): string {
        $url = $this->getPath();
        $pathToSubtract  = $this->getUri();
        $baseUrl = parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        if ($port) {
            $baseUrl .= ":" . $port;
        }
        $parsedUrl = parse_url($url, PHP_URL_PATH);
        $pos = strrpos($parsedUrl, $pathToSubtract);
        if ($pos !== false && $pos + strlen($pathToSubtract) === strlen($parsedUrl)) {
            $resultingPath = substr($parsedUrl, 0, $pos);
        } else {
            $resultingPath = $parsedUrl;
        }
        $finalUrl = $baseUrl . $resultingPath;
        return $finalUrl;
    }

    public function input($key, $def = null) {
        if ($this->isGET()) {
            return $this->query->get($key, $def);
        }

        return $this->request->get($key, $def);
    }

    public function getHeaders(): array {
        return $this->headers->all();
    }

    public function getMethods(): string {
        return $this->server->get('REQUEST_METHOD');
    }

    public function isGET(): bool {
        return $this->getMethods() === 'GET';
    }

    public function isPOST(): bool {
        return $this->getMethods() === 'POST';
    }

    public function isPUT(): bool {
        return $this->getMethods() === 'PUT';
    }

    public function isDELETE(): bool {
        return $this->getMethods() === 'DELETE';
    }

    public function test() {
        return $this->request->all();
    }
}