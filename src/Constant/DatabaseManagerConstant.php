<?php

namespace App\Constant;

class DatabaseManagerConstant
{
    public const DATABASE_TYPE_MYSQL = 0;
    // public const DATABASE_TYPE_SQLITE = 1;
    // public const DATABASE_TYPE_MONGODB = 2;

    public const DATABASE_TYPES = [
        self::DATABASE_TYPE_MYSQL => "mysql",
        // self::DATABASE_TYPE_SQLITE => "sqlite",
        // self::DATABASE_TYPE_MONGODB => "mongodb"
    ];
}
