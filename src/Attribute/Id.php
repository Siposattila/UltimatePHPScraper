<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Id
{
    private ?Column $idColumn = null;

    public function __construct(
        public readonly bool $generated = false
    ) {
    }

    public function setIdColumn(Column $column): void
    {
        $this->idColumn = $column;
    }

    public function getIdColumn(): ?Column
    {
        return $this->idColumn;
    }
}
