<?php

namespace App\ObjectManager;

use App\Constant\ObjectManagerConstant;
use App\DatabaseManager\DatabaseManager;

/**
 * @template T of object
 */
class ObjectManager
{
    /**
     * @psalm-var class-string<T>
     */
    private string $entityClass;
    private ObjectData $objectData;

    /**
     * @var object[] $objects
     * @psalm-var list<T>
     */
    private $objects;
    private array $notFlushedObjects;
    protected DatabaseManager $databaseManager;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
        $this->objectData = new ObjectData($entityClass);
        $this->databaseManager = new DatabaseManager();

        $this->objects = $this->findAll();
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function find(int $id)
    {
        $i = 0;
        while($i < count($this->objects) && $this->objects[$i]->getId() != $id) {
            if ($this->objects[$i]->getId() > $id) {
                $i = count($this->objects);
            }
            $i++;
        }

        if ($i < count($this->objects)) {
            return $this->objects[$i];
        }

        return null;
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        // TODO: implement
    }

    /**
     * @psalm-return list<T>
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * @return object[]
     * @psalm-return list<T>
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null)
    {
        // TODO: implement
        return [];
    }

    /**
     * @param object $entity
     * @psalm-param T
     * 
     * @return void
     */
    public function persist($entity)
    {
        $this->objects[] = $entity;
        $this->notFlushedObjects[] = [
            "index" => count($this->objects) - 1,
            "action" => ObjectManagerConstant::OBJECT_MANAGER_PERSIST
        ];
    }

    /**
     * @param object $entity
     * @psalm-param T
     * 
     * @return void
     */
    public function remove($entity)
    {
        $i = 0;
        while($i < count($this->objects) && $this->objects[$i]->getId() != $entity->getId()) {
            $i++;
        }

        if ($i < count($this->objects)) {
            $this->notFlushedObjects[] = [
                "index" => $this->objects[$i]->getId(),
                "action" => ObjectManagerConstant::OBJECT_MANAGER_REMOVE
            ];
            unset($this->objects[$i]);
        }
    }

    /**
     * @return void
     */
    public function flush()
    {
        foreach($this->notFlushedObjects as $key => $notFlushedObject) {
            if ($notFlushedObject["action"] == ObjectManagerConstant::OBJECT_MANAGER_PERSIST) {
                // TODO: database save or update
            }

            if ($notFlushedObject["action"] == ObjectManagerConstant::OBJECT_MANAGER_REMOVE) {
                // TODO: database delete
            }

            unset($this->notFlushedObjects[$key]);
        }
    }
}
