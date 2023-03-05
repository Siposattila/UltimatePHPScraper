<?php

namespace App\ObjectManager;

use App\Attribute\Column;
use App\Attribute\Entity;
use App\Attribute\Id;
use App\Exception\ObjectManagerNotValidEntityException;
use ReflectionClass;

class ObjectData
{
    private ReflectionClass $reflectionClass;
    private Entity $classAttribute;

    public function __construct(string $entityClass)
    {
        $this->reflectionClass = new ReflectionClass($entityClass);

        $entity = $this->reflectionClass->getAttributes(Entity::class);
        if (empty($entity)) {
            throw new ObjectManagerNotValidEntityException($entityClass . " is not a valid entity since it has no " . Entity::class . " attribute!");
        }
        $this->classAttribute = $entity[0]->newInstance();
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
        $properties = $this->reflectionClass->getProperties();
        foreach ($properties as $property) {
            $column = $property->getAttributes(Column::class);
            if (!empty($column)) {
                $result[] = $column[0]->newInstance();
            }
        }

        return $result;
    }

    public function getIdField(): ?Id
    {
        $properties = $this->reflectionClass->getProperties();
        foreach ($properties as $property) {
            if ($property->getName() == "id") {
                $id = $property->getAttributes(Id::class)[0]->newInstance();
                $id->setIdColumn($property->getAttributes(Column::class)[0]->newInstance());

                return $id;
            }
        }

        return null;
    }
}
