<?php

namespace App\DatabaseManager;

use App\Constant\DatabaseManagerConstant;
use App\DatabaseManager\Database\Mysql;
use App\DatabaseManager\Database\QueryInterface;
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
    private string $alias;

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

    public function createQueryBuilder(?string $alias = null): QueryInterface
    {
        $this->alias = $alias;

        $interface = null;
        if ($this->databaseType == DatabaseManagerConstant::DATABASE_TYPE_MYSQL) {
            $interface = new Mysql($this->user, $this->password, $this->host, $this->port, $this->database, $this->options);
        }

        return $interface;
    }
}
