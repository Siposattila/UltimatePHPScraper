<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(
        public readonly string $repositoryClass,
        public readonly string $tableName = ""
    ) {
    }
}
