<?php

namespace App\Includes;

class Logger {
    private static $logDirectory = __DIR__ . '/../storage/logs';
    private static $logFile = null;
    private static $singleFile = false;

    private static function getLogFile() {
        if (self::$singleFile && self::$logFile) {
            return self::$logFile;
        }

        $date = date('Y-m-d');
        $fileName = self::$singleFile ? 'acrylic.log' : "acrylic-{$date}.log";

        return self::$logDirectory . '/' . $fileName;
    }

    private static function createLogDirectory() {
        if (!file_exists(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0700, true);
        }
    }

    private static function writeLog($level, $message) {
        self::createLogDirectory();

        $filePath = self::getLogFile();
        $time = date('Y-m-d H:i:s');
        $formattedMessage = "[$time] $level: $message" . PHP_EOL;

        file_put_contents($filePath, $formattedMessage, FILE_APPEND);
    }

    public static function info($message) {
        self::writeLog('INFO', $message);
    }

    public static function warning($message) {
        self::writeLog('WARNING', $message);
    }

    public static function error($message) {
        self::writeLog('ERROR', $message);
    }

    public static function debug($message) {
        self::writeLog('DEBUG', print_r($message, true));
    }

    public static function exception($exception) {
        $message = $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();
        self::writeLog('EXCEPTION', $message);
    }

    public static function useSingleFile($fileName = 'acrylic.log') {
        self::$singleFile = true;
        self::$logFile = self::$logDirectory . '/' . $fileName;
    }

    public static function useDailyFiles() {
        self::$singleFile = false;
        self::$logFile = null;
    }
}