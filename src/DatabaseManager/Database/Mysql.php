<?php

namespace App\DatabaseManager\Database;

use App\DatabaseManager\Expression\ExpressionInterface;
use App\DatabaseManager\Expression\Mysql as ExpressionMysql;
use App\ObjectManager\ObjectData;
use PDO;
use PDOStatement;

class Mysql extends AbstractDatabase implements QueryInterface
{
    private PDO $pdo;
    private PDOStatement $statement;
    public ExpressionInterface $expression;

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

    public function orderBy(string $column, string $order = "ASC"): self
    {
        $this->queryElements["order"][$column] = $order;
        return $this;
    }

    public function limit(?int $limit = null): self
    {
        $this->queryElements["limit"] = $limit;
        return $this;
    }

    public function ensureDatabaseCreated(): void
    {
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS ".$this->database.";");
    }

    public function ensureTableCreated(ObjectData $objectData): void
    {
        $count = $this->pdo->query("SELECT COUNT(TABLE_NAME)
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA LIKE ".$this->database." AND TABLE_TYPE LIKE 'BASE TABLE'
            AND TABLE_NAME = '".$objectData->getTableName()."';")
            ->fetch();

        if ($count <= 0) {
            $columns = "";
            foreach ($objectData->getColumnFields() as $column) {
                if ($column->columnName == "id") {
                    $id = $objectData->getIdField();
                    $auto = ($id->generated)?"AUTO_INCREMENT":"";
                    $columns .= "id INT $auto PRIMARY KEY,";
                }
                else {
                    // $type = DatabaseManagerConstant::DATABASE_COLUMN_TYPES[DatabaseManagerConstant::DATABASE_TYPE_MYSQL]
                    // $columns .= $column->columnName." ".;
                }
            }

            $this->pdo->exec("CREATE TABLE ($columns);");
        }
    }

    public function setParameter(string $name, string $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function getQuery(): self
    {
        $query = "";

        $query .= $this->expression->select($this->queryElements["select"]["columns"]);
        $query .= $this->expression->from($this->queryElements["from"]["table"], $this->queryElements["from"]["alias"]);
        $query .= $this->expression->where();

        foreach ($this->queryElements["where"]["and"] as $and) {
            $query .= $this->expression->and($query, $and);
        }

        foreach ($this->queryElements["where"]["or"] as $or) {
            $query .= $this->expression->or($query, $or);
        }

        foreach ($this->queryElements["order"] as $column => $order) {
            $query .= $this->expression->orderBy($column, $order);
        }

        $query .= $this->expression->limit($this->queryElements["limit"]);

        $this->query = $query.";";
        return $this;
    }

    public function execute(): array
    {
        $this->statement->execute($this->parameters);
        return $this->statement->fetchAll();
    }
}
