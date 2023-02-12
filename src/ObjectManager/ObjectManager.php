<?php

namespace App\ObjectManager;

use App\Constant\ObjectManagerConstant;
use App\DatabaseManager\DatabaseManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
    private Serializer $serializer;

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
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->databaseManager = new DatabaseManager();

        $this->objects = $this->findAll();
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function find(int $id)
    {
        return $this->findBy(["id" => $id], null, 1)[0];
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->findBy($criteria, $orderBy, 1)[0];
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
        if (empty($this->objects)) {
            $queryBuilder = $this->databaseManager->createQueryBuilder();
            $queryBuilder = $queryBuilder->select([])
                ->from($this->objectData->getTableName());

            foreach ($criteria as $key => $crit) {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expression->equal($key, $crit));
            }

            foreach ($orderBy as $key => $order) {
                $queryBuilder = $queryBuilder->orderBy($key, $order);
            }

            $this->objects = $queryBuilder->limit($limit)
                ->getQuery()
                ->execute();
        }

        if (empty($criteria)) {
            return $this->objects;
        }

        $result = [];
        $criteriaJson = json_encode($criteria);
        foreach ($this->objects as $object) {
            $objectArray = json_decode($this->serializer->serialize($object, "json"), true);
            $keys = array_keys($criteria);
            $compareHelper = [];

            foreach ($keys as $critKey) {
                $compareHelper[$critKey] = $objectArray[$critKey];
            }

            if (strcmp(json_encode($compareHelper), $criteriaJson) == 0) {
                $result[] = $object;
            }
        }

        return $result;
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
