<?php

namespace App\Constant;

class MysqlConstant
{
    public const MYSQL_TYPE_VARCHAR = 0;
    public const MYSQL_TYPE_INT = 1;
    // public const MYSQL_TYPE_ = 0;
    // public const MYSQL_TYPE_ = 0;
    // public const MYSQL_TYPE_ = 0;

    public const MYSQL_TYPES = [
        self::MYSQL_TYPE_VARCHAR => "string",
        self::MYSQL_TYPE_INT => "integer",
    ];

    public const MYSQL_REAL_TYPES = [
        self::MYSQL_TYPE_VARCHAR => "VARCHAR",
        self::MYSQL_TYPE_INT => "INT",
    ];

    public const MYSQL_REAL_TYPES_LENGTH = [
        self::MYSQL_TYPE_VARCHAR => "255",
        self::MYSQL_TYPE_INT => "11",
    ];
}
