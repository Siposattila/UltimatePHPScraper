<?php

namespace App\DatabaseManager;

interface QueryInterface
{
    public function get(): array;
    public function getAll(): array;
    public function insert(): void;
    public function update(): void;
    public function delete(): void;
}
