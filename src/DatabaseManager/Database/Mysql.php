<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\Mysql as ExpressionMysql;
use PDO;
use PDOStatement;

class Mysql extends AbstractDatabase implements QueryInterface
{
    private PDO $pdo;
    private PDOStatement $statement;

    public function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly array $options
    )
    {
        $this->query = "";
        $this->parameters = [];
        $this->queryElements = [
            "select" => ["columns" => []],
            "from" => ["alias" => "", "table" => ""],
            "where" => ["and" => [], "or" => []],
            "order" => [],
            "limit" => null
        ];
        $this->expression = new ExpressionMysql();
        $this->getConnection();
    }

    protected function getConnection(): void
    {
        $dsn = "mysql:host=".$this->host.":".$this->port.";dbname=".$this->database.";charset=".$this->options["charset"];
        $this->pdo = new PDO($dsn, $this->user, $this->password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);
    }

    public function select(array $columns = []): self
    {
        $this->queryElements["select"]["columns"] = $columns;
        return $this;
    }

    public function from(string $table, string $alias = ""): self
    {
        $this->queryElements["from"]["table"] = $table;
        $this->queryElements["from"]["alias"] = $alias;

        return $this;
    }

    public function insert(array $columns, array $values): int
    {
        $insert = "INSERT INTO Customers (".implode(", ", $columns).") VALUES (".implode(", ", $values).")";
        $this->pdo->exec($insert);

        return $this->pdo->lastInsertId();
    }

    public function update(): void
    {
        // TODO: implement
    }

    public function delete(): void
    {
        // TODO: implement
    }

    public function andWhere(string $where): self
    {
        $this->queryElements["where"]["and"][] = $where;
        return $this;
    }

    public function orWhere(string $where): self
    {
        $this->queryElements["where"]["or"][] = $where;
        return $this;
    }

    public function setParameter(string $name, string $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function getQuery(string $query): self
    {
        // TODO: build query from queryElements
        $this->query = $query;
        return $this;
    }

    public function execute(): array
    {
        $this->statement->execute($this->parameters);
        return $this->statement->fetchAll();
    }
}