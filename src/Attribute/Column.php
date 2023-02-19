<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    // public const PHP_TYPE_STRING = 0;
    // public const PHP_TYPE_INTEGER = 1;

    // public const PHP_TYPES = [
    //     self::PHP_TYPE_STRING => string::class,
    //     self::PHP_TYPE_INTEGER => int::class
    // ];

    // // Ofc there is more! But right now this is good enough.
    // public const DATABASE_COLUMN_TYPES = [
    //     self::DATABASE_TYPE_MYSQL => [
    //         self::PHP_TYPE_STRING => "VARCHAR",
    //         self::PHP_TYPE_INTEGER => "INT",
    //     ],
    //     // self::DATABASE_TYPE_SQLITE => [],
    //     // self::DATABASE_TYPE_MONGODB => []
    // ];

    public function __construct(
        public readonly ?string $columnName = null,
        public readonly ?string $columnType = null,
        public readonly bool $nullable = false,
        public readonly ?int $length = null
    ) {
    }
}
