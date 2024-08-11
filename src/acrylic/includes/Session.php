<?php

namespace App\Includes;

class Session {
    public static $sessionSavePath = __DIR__ . '/../storage/sessions';
    private static $sessionIdLength = 32;
    private static $sessionFile;
    private static $sessionLifetime = 86400 * 7;

    public static function start() {
        self::garbageCollect();

        if (isset($_COOKIE['SESSION_ID'])) {
            $sessionId = $_COOKIE['SESSION_ID'];
        } else {
            $sessionId = self::generateUniqueSessionId();
            setcookie('SESSION_ID', $sessionId, time() + self::$sessionLifetime, "/");
        }

        if (!file_exists(self::$sessionSavePath)) {
            mkdir(self::$sessionSavePath, 0700, true);
        }

        self::$sessionFile = self::$sessionSavePath . '/' . $sessionId;

        if (file_exists(self::$sessionFile)) {
            $sessionData = file_get_contents(self::$sessionFile);
            $_SESSION = unserialize($sessionData);
        } else {
            $_SESSION = [];
        }

        register_shutdown_function([__CLASS__, 'save']);
    }

    public static function save() {
        if (self::$sessionFile) {
            file_put_contents(self::$sessionFile, serialize($_SESSION));
        }
    }

    private static function generateSessionId() {
        return bin2hex(random_bytes(self::$sessionIdLength / 2));
    }

    private static function generateUniqueSessionId() {
        do {
            $sessionId = self::generateSessionId();
            $sessionFile = self::$sessionSavePath . '/' . $sessionId;
        } while (file_exists($sessionFile));

        return $sessionId;
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function delete($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        if (isset($_COOKIE['SESSION_ID'])) {
            $sessionId = $_COOKIE['SESSION_ID'];
            $sessionFile = self::$sessionSavePath . '/' . $sessionId;

            if (file_exists($sessionFile)) {
                unlink($sessionFile);
            }

            setcookie('SESSION_ID', '', time() - 3600, "/");
            $_SESSION = [];
        }
    }

    private static function garbageCollect() {
        if (!is_dir(self::$sessionSavePath)) {
            return;
        }

        $files = scandir(self::$sessionSavePath);
        $now = time();

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = self::$sessionSavePath . '/' . $file;
                if (is_file($filePath) && ($now - filemtime($filePath)) > self::$sessionLifetime) {
                    unlink($filePath);
                }
            }
        }
    }
}