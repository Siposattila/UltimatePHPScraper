<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\ExpressionInterface;

interface QueryInterface
{
    private array $parameters;
    public ExpressionInterface $expression;

    public function select(): self;
    public function from(): self;
    public function where(): self;
    public function insert(): self;
    public function update(): self;
    public function delete(): self;
    public function andWhere(): self;
    public function orWhere(): self;
    public function setParameter(string $name, string $value): self;
    public function setQuery(string $query): void;
    public function execute(): array;
}
