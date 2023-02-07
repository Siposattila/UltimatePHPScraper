<?php

namespace App\DatabaseManager;

class DatabaseQuery
{
    private QueryInterface $queryInterface;

    public function __construct(QueryInterface $queryInterface)
    {
        $this->queryInterface = $queryInterface;
    }


}
