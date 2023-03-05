<?php

namespace App\Entity;

use App\Attribute\Column;
use App\Attribute\Id;

class BaseEntity
{
    #[Id(generated: true)]
    #[Column(columnName: "id", columnType: "integer")]
    private ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
