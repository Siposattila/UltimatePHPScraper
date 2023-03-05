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

    /**
     * @var Flush[] $notFlushedObjects
     */
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
        return $this->findBy([$this->objectData->getIdField()->getIdColumn()->columnName => $id], null, 1)[0];
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

        foreach ($result as $key => $object) {
            $result[$key] = $this->serializer->deserialize(json_encode($object), $this->entityClass, "json");
        }

        return $result;
    }

    /**
     * @param object $entity
     * @psalm-param T
     * 
     * @return void
     */
    protected function persist($entity): void
    {
        $this->objects[] = $entity;
        $this->notFlushedObjects[] = new Flush(
            count($this->objects) - 1,
            is_null($entity->getId()),
            ObjectManagerConstant::OBJECT_MANAGER_PERSIST
        );
    }

    /**
     * @param object $entity
     * @psalm-param T
     * 
     * @return void
     */
    protected function remove($entity): void
    {
        $i = 0;
        while ($i < count($this->objects) && $this->objects[$i]->getId() != $entity->getId()) {
            $i++;
        }

        if ($i < count($this->objects)) {
            $this->notFlushedObjects[] = new Flush(
                $this->objects[$i]->getId(),
                false,
                ObjectManagerConstant::OBJECT_MANAGER_REMOVE
            );
            unset($this->objects[$i]);
        }
    }

    /**
     * @return void
     */
    protected function flush(): void
    {
        foreach ($this->notFlushedObjects as $key => $notFlushedObject) {
            if ($notFlushedObject->getAction() == ObjectManagerConstant::OBJECT_MANAGER_PERSIST) {
                if ($notFlushedObject->getIsNew()) {
                    $this->objects[$notFlushedObject->getIndex()]->setId($this->insert($this->objects[$notFlushedObject->getIndex()]));
                }

                if (!$notFlushedObject->getIsNew()) {
                    $this->update($this->objects[$notFlushedObject->getIndex()]);
                }
            }

            if ($notFlushedObject->getAction() == ObjectManagerConstant::OBJECT_MANAGER_REMOVE) {
                $this->delete($notFlushedObject->getIndex());
            }

            unset($this->notFlushedObjects[$key]);
        }
    }

    private function insert(object $entity): int
    {
        $entityJson = json_decode($this->serializer->serialize($entity, "json"), true);
        $idColumn = $this->objectData->getIdField();
        if ($idColumn->generated) {
            unset($entityJson[$idColumn->getIdColumn()->columnName]);
        }

        return $this->databaseManager->createQueryBuilder()->insert(
            $this->objectData->getTableName(),
            array_keys($entityJson),
            array_values($entityJson)
        );
    }

    private function update(object $entity): void
    {
        $entityJson = json_decode($this->serializer->serialize($entity, "json"), true);
        $entityOldJson = json_decode($this->serializer->serialize($this->find($entity->getId()), "json"), true);
        $difference = $this->objectDifference($entityJson, $entityOldJson);
        $idColumn = $this->objectData->getIdField();
        if ($idColumn->generated && isset($difference[$idColumn->getIdColumn()->columnName])) {
            unset($difference[$idColumn->getIdColumn()->columnName]);
        }

        $this->databaseManager->createQueryBuilder()->update(
            $this->objectData->getTableName(),
            $idColumn->getIdColumn()->columnName,
            $entity->getId(),
            array_keys($difference),
            array_values($difference)
        );
    }

    private function delete(int $id): void
    {
        $this->databaseManager->createQueryBuilder()->delete(
            $this->objectData->getTableName(),
            $this->objectData->getIdField()->getIdColumn()->columnName,
            $id
        );
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
