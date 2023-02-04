<?php

namespace App\Entity;

class KawasakiMotor extends BaseEntity
{
    private ?string $type = null;
    private ?string $year = null;
    private ?string $model = null;
    private ?string $vin = null;
    private ?string $engine = null;
    private ?string $country = null;
    private ?array $colors = null;
    private ?string $catalog = null;

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
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

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): self
    {
        $this->vin = $vin;
        return $this;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function setEngine(?string $engine): self
    {
        $this->engine = $engine;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(?array $colors): self
    {
        $this->colors = $colors;
        return $this;
    }

    public function getCatalog(): ?string
    {
        return $this->catalog;
    }

    public function setCatalog(?string $catalog): self
    {
        $this->catalog = $catalog;
        return $this;
    }
}
