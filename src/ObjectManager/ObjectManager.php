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
        $this->objectData = new ObjectData($this->entityClass);
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->databaseManager = new DatabaseManager();

        $this->databaseManager->createQueryBuilder()->ensureDatabaseCreated();
        $this->databaseManager->createQueryBuilder()->ensureTableCreated($this->objectData);

        $this->objects = $this->findAll();
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function find(int $id): object
    {
        return $this->findBy(["id" => $id], null, 1)[0];
    }

    /**
     * @return object|null
     * @psalm-return ?T
     */
    public function findOneBy(array $criteria, array $orderBy = null): object
    {
        return $this->findBy($criteria, $orderBy, 1)[0];
    }

    /**
     * @psalm-return list<T>
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    /**
     * @return object[]
     * @psalm-return list<T>
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null): array
    {
        if (empty($this->objects)) {
            $queryBuilder = $this->databaseManager->createQueryBuilder();
            $queryBuilder = $queryBuilder->select([])
                ->from($this->objectData->getTableName());

            foreach ($criteria as $key => $crit) {
                $queryBuilder = $queryBuilder->andWhere($queryBuilder->expression->equal($key, $crit));
            }

            if (!is_null($orderBy)) {
                foreach ($orderBy as $key => $order) {
                    $queryBuilder = $queryBuilder->orderBy($key, $order);
                }
            }

            if (!is_null($limit)) {
                $queryBuilder->limit($limit);
            }

            $this->objects = $queryBuilder->getQuery()
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
    public function persist($entity): void
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
    public function remove($entity): void
    {
        $i = 0;
        while ($i < count($this->objects) && $this->objects[$i]->getId() != $entity->getId()) {
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
    public function flush(): void
    {
        foreach ($this->notFlushedObjects as $key => $notFlushedObject) {
            if ($notFlushedObject["action"] == ObjectManagerConstant::OBJECT_MANAGER_PERSIST) {
                // TODO: database save or update
            }

            if ($notFlushedObject["action"] == ObjectManagerConstant::OBJECT_MANAGER_REMOVE) {
                // TODO: database delete
            }

            unset($this->notFlushedObjects[$key]);
        }
    }

    private function insert(object $entity): void
    {
        $entityJson = $this->serializer->serialize($entity);
        $this->databaseManager->createQueryBuilder()->insert(
            $this->objectData->getTableName(),
        );
    }

    private function update(): void
    {
        // TODO: implement
    }

    private function delete(): void
    {
        // TODO: implement
    }

    private function objectDifference(array $new, array $old): array
    {
        $difference = [];
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $old)) {
                if (is_array($value)) {
                    $recursiveDifference = $this->objectDifference($value, $old[$key]);
                    if (count($recursiveDifference)) {
                        $difference[$key] = $recursiveDifference;
                    }
                } else {
                    if ($value != $old[$key]) {
                        $difference[$key] = $value;
                    }
                }
            }
        }

        return $difference;
    }
}
