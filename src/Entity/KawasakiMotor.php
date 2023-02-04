<?php

namespace App\Entity;

class KawasakiMotor extends BaseEntity
{
    private ?string $type;
    private ?string $year;
    private ?string $modell;
    private ?string $vin;
    private ?string $motor;
    private ?string $country;

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

    public function getModell(): ?string
    {
        return $this->modell;
    }

    public function setModell(?string $modell): self
    {
        $this->modell = $modell;
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

    public function getMotor(): ?string
    {
        return $this->motor;
    }

    public function setMotor(?string $motor): self
    {
        $this->motor = $motor;
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
}
