<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\Mysql as ExpressionMysql;
use PDO;
use PDOStatement;

// TODO: implement
class Mysql extends AbstractDatabase implements QueryInterface
{
    private PDO $pdo;
    private PDOStatement $statement;
    private string $query;

    public function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly array $options
    )
    {
        $this->expression = new ExpressionMysql();
        $this->getConnection();
    }

    protected function getConnection(): void
    {
        $dsn = "mysql:host=".$this->host.":".$this->port.";dbname=".$this->database.";charset=".$this->options["charset"];
        $this->pdo = new PDO($dsn, $this->user, $this->password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);
    }

    public function select(): self
    {
        return $this;
    }

    public function from(): self
    {
        return $this;
    }

    public function where(): self
    {
        return $this;
    }

    public function insert(): self
    {
        return $this;
    }

    public function update(): self
    {
        return $this;
    }

    public function delete(): self
    {
        return $this;
    }

    public function andWhere(): self
    {
        return $this;
    }

    public function orWhere(): self
    {
        return $this;
    }

    public function setParameter(string $name, string $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function execute(): array
    {
        $this->statement->execute($this->parameters);
        return $this->statement->fetchAll();
    }
}
