<?php

namespace App\Entity;

class BaseEntity
{
    // FIXME: make id generated
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
