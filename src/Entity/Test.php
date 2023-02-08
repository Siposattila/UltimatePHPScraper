<?php

namespace App\Entity;

use App\Attribute\Entity;
use App\Repository\TestRepository;

#[Entity(repositoryClass: TestRepository::class)]
class Test extends BaseEntity
{
    private ?string $name = null;
    private ?string $age = null;
    private ?string $year = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;
        return $this;
    }
}
