<?php

namespace App\DatabaseManager\Database;

abstract class AbstractDatabase
{
    protected abstract function getConnection(): void;
}
