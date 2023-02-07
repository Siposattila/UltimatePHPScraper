<?php

namespace App\DatabaseManager\Expression;

interface ExpressionInterface
{
    public function select(array $columns);
    public function from(string $table);
    public function where();
    public function or(string $x, string $y);
    public function and(string $x, string $y);
    public function limit(int $x);
    public function orderBy(string $column, string $order);
    public function having(string $x);
    public function like(string $x, string $y);
    public function between(string $x, string $y, string $z);
    public function equal(string $x, string $y);
    public function notEqual(string $x, string $y);
}
