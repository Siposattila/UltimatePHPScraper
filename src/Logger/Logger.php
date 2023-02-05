<?php

namespace App\Logger;

use App\Constant\LoggerConstant;

// TODO: maybe we should put a little trace as well in the logs :^)
class Logger
{
    public static function debug(mixed $log, bool $isShow = true): void
    {
        if ($isShow) {
            var_dump($log);
        }

        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_MAIN_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_DEBUG_CODE].$log . PHP_EOL,
            FILE_APPEND
        );
    }

    public static function warning(mixed $log): void
    {
        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_MAIN_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_WARNING_CODE].$log . PHP_EOL,
            FILE_APPEND
        );
    }

    public static function error(mixed $log): void
    {
        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_MAIN_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_ERROR_CODE].$log . PHP_EOL,
            FILE_APPEND
        );

        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_ERROR_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_ERROR_CODE].$log . PHP_EOL,
            FILE_APPEND
        );
    }

    public static function success(mixed $log): void
    {
        var_dump($log);
        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_MAIN_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_SUCCESS_CODE].$log . PHP_EOL,
            FILE_APPEND
        );
    }

    public static function info(mixed $log): void
    {
        file_put_contents(
            LoggerConstant::LOGGER_LOGS_PATH.LoggerConstant::LOGGER_MAIN_FILE,
            LoggerConstant::LOGGER_PREFIXES[LoggerConstant::LOGGER_INFO_CODE].$log . PHP_EOL,
            FILE_APPEND
        );
    }
}
