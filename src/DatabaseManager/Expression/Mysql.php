<?php

namespace App\DatabaseManager\Expression;

class Mysql implements ExpressionInterface
{
    public function select(array $columns = []): string
    {
        if (empty($columns)) {
            return "SELECT *";
        }

        return "SELECT ".implode(", ", $columns);
    }

    public function from(string $table): string
    {
        return "FROM $table";
    }

    public function where(): string
    {
        return "WHERE";
    }

    public function or(string $x, string $y): string
    {
        return "$x OR $y";
    }

    public function and(string $x, string $y): string
    {
        return "$x AND $y";
    }

    public function limit(int $x): string
    {
        return "LIMIT $x";
    }

    public function orderBy(string $column, string $order): string
    {
        return "ORDER BY $column $order";
    }

    public function having(string $x): string
    {
        return "HAVING $x";
    }

    public function like(string $x, string $y): string
    {
        return "$x LIKE $y";
    }

    public function between(string $x, string $y, string $z): string
    {
        return "$x BETWEEN $y AND $z";
    }

    public function equal(string $x, string $y): string
    {
        return "$x = $y";
    }

    public function notEqual(string $x, string $y): string
    {
        return "$x != $y";
    }
}
