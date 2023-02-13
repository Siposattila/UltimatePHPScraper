<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public readonly ?string $columnName = null,
        public readonly ?string $columnType = null,
        public readonly bool $nullable = false,
        public readonly ?int $length = null
    ) {
    }
}
