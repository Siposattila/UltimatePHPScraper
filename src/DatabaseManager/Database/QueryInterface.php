<?php

namespace App\DatabaseManager\Database;

use App\ObjectManager\ObjectData;

interface QueryInterface
{
    public function select(array $columns): self;
    public function from(string $table, string $alias = ""): self;
    public function insert(string $table, array $columns, array $values): int;
    public function update(string $table, string $idColumn, int $id, array $columns, array $values): void;
    public function delete(string $table, string $idColumn, int $id): void;
    public function where(string $where): self;
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
