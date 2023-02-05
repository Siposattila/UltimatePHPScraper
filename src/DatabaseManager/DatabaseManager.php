<?php

namespace App\DatabaseManager;

use App\Constant\DatabaseManagerConstant;
use App\Exception\DatabaseManagerException;

class DatabaseManager
{
    private string $user;
    private string $password;
    private string $host;
    private string $port;
    private string $database;
    private array $options;
    private int $databaseType;
    private Mysql $mysql;

    public function __construct()
    {
        // FIXME: This is ugly and not very efficent
        $connectionString = $_ENV["DATABASE_URL"];
        $databaseType = explode("://", $connectionString)[0];
        $connectionString = explode("://", $connectionString)[1];

        if (!isset(array_flip(DatabaseManagerConstant::DATABASE_TYPES)[$databaseType])) {
            throw new DatabaseManagerException("The given database type is not supported!");
        }

        $this->databaseType = array_flip(DatabaseManagerConstant::DATABASE_TYPES)[$databaseType];
        $this->user = explode(":", explode("@", $connectionString)[0])[0];
        $this->password = explode(":", explode("@", $connectionString)[0])[1];
        $connectionString = explode("@", $connectionString)[1];
        $this->host = explode(":", $connectionString)[0];
        $this->port = explode("/", explode(":", $connectionString)[1])[0];
        $connectionString = explode("/", explode(":", $connectionString)[1])[1];
        $this->database = explode("?", $connectionString)[0];
        $connectionString = explode("?", $connectionString)[1];

        $options = [];
        foreach(explode("&", $connectionString) as $option) {
            $options[explode("=", $option)[0]] = explode("=", $option)[1];
        }
        $this->options = $options;
    }

    public function createQueryBuilder(?string $alias): DatabaseQuery
    {
        // TODO: implement
        return new DatabaseQuery();
    }
}
