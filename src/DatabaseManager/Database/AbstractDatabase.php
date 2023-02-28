<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\ExpressionInterface;

abstract class AbstractDatabase
{
    protected string $query;
    protected array $parameters;
    protected array $queryElements;
    public ExpressionInterface $expression;

    protected abstract function getConnection(): void;
}
