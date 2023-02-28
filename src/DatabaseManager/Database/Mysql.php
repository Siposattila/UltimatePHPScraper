<?php

namespace App\DatabaseManager\Database;

use App\Constant\MysqlConstant;
use App\DatabaseManager\Expression\ExpressionInterface;
use App\DatabaseManager\Expression\Mysql as ExpressionMysql;
use App\ObjectManager\ObjectData;
use PDO;
use PDOStatement;

class Mysql extends AbstractDatabase implements QueryInterface
{
    private PDO $pdo;
    private ?PDOStatement $statement = null;
    public ExpressionInterface $expression;

    public function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $host,
        private readonly string $port,
        private readonly string $database,
        private readonly array $options
    ) {
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
        $dsn = "mysql:host=" . $this->host . ":" . $this->port . ";dbname=" . $this->database . ";charset=" . $this->options["charset"];
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

    public function insert(string $table, array $columns, array $values): int
    {
        $insert = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");";
        $this->pdo->exec($insert);

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, int $id, array $columns, array $values): void
    {
        $update = "UPDATE $table " . implode(", ", array_map(function ($column, $value) {
            return $column . " = " . $value;
        }, $columns, $values)) . " WHERE id = $id;";
        $this->pdo->exec($update);
    }

    public function delete(string $table, int $id): void
    {
        $delete = "DELETE FROM $table WHERE id = $id;";
        $this->pdo->exec($delete);
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
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS " . $this->database . ";");
    }

    public function ensureTableCreated(ObjectData $objectData): void
    {
        $count = $this->pdo->query("SELECT COUNT(TABLE_NAME) AS count 
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA LIKE '" . $this->database . "' AND TABLE_TYPE LIKE 'BASE TABLE'
            AND TABLE_NAME = '" . $objectData->getTableName() . "';")
            ->fetch()->count;

        // TODO: rework
        if ($count <= 0) {
            $columns = "";
            $types = array_flip(MysqlConstant::MYSQL_TYPES);
            foreach ($objectData->getColumnFields() as $column) {
                if ($column->columnName == "id") {
                    $id = $objectData->getIdField();
                    $auto = ($id->generated) ? "AUTO_INCREMENT" : "";
                    $columns .= "id INT $auto PRIMARY KEY, ";
                } else {
                    $type = MysqlConstant::MYSQL_REAL_TYPES[$types[$column->columnType]];
                    if (!is_null($column->length)) {
                        $type .= "(" . $column->length . ")";
                    }

                    if (is_null($column->length)) {
                        $type .= "(" . MysqlConstant::MYSQL_REAL_TYPES_LENGTH[$types[$column->columnType]] . ")";
                    }

                    $columns .= $column->columnName . " " . $type . " ";

                    if ($column->nullable) {
                        $columns .= "DEFAULT NULL, ";
                    }

                    if (!$column->nullable) {
                        $columns .= "NOT NULL, ";
                    }
                }
            }

            $columns[strlen($columns) - 2] = " ";
            var_dump($columns);
            $this->pdo->exec("CREATE TABLE " . $objectData->getTableName() . " ($columns);");
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

        if (!empty($this->queryElements["where"]["and"]) || !empty($this->queryElements["where"]["or"])) {
            $query .= $this->expression->where();
        }

        foreach ($this->queryElements["where"]["and"] as $and) {
            $query .= $this->expression->and($query, $and);
        }

        foreach ($this->queryElements["where"]["or"] as $or) {
            $query .= $this->expression->or($query, $or);
        }

        foreach ($this->queryElements["order"] as $column => $order) {
            $query .= $this->expression->orderBy($column, $order);
        }

        if (!is_null($this->queryElements["limit"])) {
            $query .= $this->expression->limit($this->queryElements["limit"]);
        }

        $this->query = $query . ";";
        var_dump($query); // TODO: need to delete
        return $this;
    }

    public function execute(): array
    {
        $this->statement = $this->pdo->prepare($this->query);
        $this->statement->execute($this->parameters);
        return $this->statement->fetchAll();
    }
}
