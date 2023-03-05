<?php

namespace App\ObjectManager;

class Flush
{
    private int $index;
    private bool $isNew;
    private int $action;

    public function __construct(int $index, bool $isNew, int $action)
    {
        $this->index = $index;
        $this->isNew = $isNew;
        $this->action = $action;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getIsNew(): bool
    {
        return $this->isNew;
    }

    public function getAction(): int
    {
        return $this->action;
    }
}
