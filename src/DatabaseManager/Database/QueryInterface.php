<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\ExpressionInterface;
use App\ObjectManager\ObjectData;

interface QueryInterface
{
    private string $query;
    private array $parameters;
    private array $queryElements;
    public ExpressionInterface $expression;

    public function select(array $columns): self;
    public function from(string $table, string $alias = ""): self;
    public function insert(array $columns, array $values): int;
    public function update(): void;
    public function delete(): void;
    public function andWhere(string $where): self;
    public function orWhere(string $where): self;
    public function orderBy(string $column, string $order = "ASC"): self;
    public function limit(?int $limit = null): self;
    public function ensureDatabaseCreated(): void;
    public function ensureTableCreated(ObjectData $objectData): void;
    public function setParameter(string $name, string $value): self;
    public function getQuery(): self;
    public function execute(): array;
}
