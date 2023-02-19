<?php

namespace App\ObjectManager;

use App\Attribute\Column;
use App\Attribute\Entity;
use App\Attribute\Id;
use App\Exception\ObjectManagerNotValidEntityException;
use ReflectionClass;
use Throwable;

class ObjectData
{
    private ReflectionClass $reflectionClass;
    private Entity $classAttribute;

    public function __construct(string $entityClass)
    {
        $this->reflectionClass = new ReflectionClass($entityClass);

        try
        {
            $this->classAttribute = $this->reflectionClass->getAttributes(Entity::class)[0]->newInstance();
        } catch(Throwable $t) {
            throw new ObjectManagerNotValidEntityException($entityClass." is not a valid entity since it has no ".Entity::class." attribute!");
        }
    }

    public function getRepository(): string
    {
        return $this->classAttribute->repositoryClass;
    }

    public function getTableName(): string
    {
        if (empty($this->classAttribute->tableName)) {
            return strtolower($this->reflectionClass->getShortName());
        }

        return $this->classAttribute->tableName;
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getFields(): array
    {
        return $this->reflectionClass->getProperties();
    }

    /**
     * @return Column[]
     */
    public function getColumnFields(): array
    {
        $result = [];
        $columns = $this->reflectionClass->getAttributes(Column::class);
        foreach ($columns as $column) {
            $result[] = $column->newInstance();
        }

        return $result;
    }

    public function getIdField(): Id
    {
        return $this->reflectionClass->getAttributes(Id::class)[0]->newInstance();
    }
}
