<?php

namespace App\Constant;

class LoggerConstant
{
    public const LOGGER_LOGS_PATH = __DIR__ . "/../Logger/logs/";
    public const LOGGER_MAIN_FILE = "main.log";
    public const LOGGER_ERROR_FILE = "error.log";

    public const LOGGER_DEBUG_CODE = 0;
    public const LOGGER_WARNING_CODE = 1;
    public const LOGGER_ERROR_CODE = 2;
    public const LOGGER_SUCCESS_CODE = 3;
    public const LOGGER_INFO_CODE = 4;

    public const LOGGER_PREFIXES = [
        self::LOGGER_DEBUG_CODE => "[Debug]::",
        self::LOGGER_WARNING_CODE => "[Warning]::",
        self::LOGGER_ERROR_CODE => "[Error]::",
        self::LOGGER_SUCCESS_CODE => "[Success]::",
        self::LOGGER_INFO_CODE => "[Info]::"
    ];
}
