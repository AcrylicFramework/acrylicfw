<?php

namespace App\Includes;

class Config {
    public static $appDir = __DIR__;
    public static function appDir(): string {
        return self::$appDir;
    }
    public static function baseUrl(): string {
        return $_ENV["BASE_PATH"] ?? 'acrylic/';
    }
}