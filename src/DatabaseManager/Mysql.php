<?php

namespace App\DatabaseManager;

use AbstractDatabase;
use PDO;

// TODO: implement
class Mysql extends AbstractDatabase implements QueryInterface
{
    private PDO $pdo;

    public function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly array $options
    )
    {
        $this->getConnection();
    }

    protected function getConnection(): PDO
    {
        // $dsn = "mysql:host=".$this->host.":".$this->port.";dbname=".$this->database;charset=".$options["charset"];
        // new PDO(, $user, $password);
    }

    public function get(): array
    {
        return [];
    }

    public function getAll(): array
    {
        return [];
    }

    public function insert(): void
    {

    }

    public function update(): void
    {

    }

    public function delete(): void
    {

    }

    protected function prepare(): void
    {
        
    }

    protected function execute(): void
    {
        
    }
}
