<?php

namespace App\Repository;

use App\Entity\Test;
use App\ObjectManager\ObjectManager;

/**
 * @extends ObjectManager<Test>
 *
 * @method Test|null find(int $id)
 * @method Test|null findOneBy(array $criteria, array $orderBy = null)
 * @method Test[]    findAll(array $criteria, array $orderBy = null)
 * @method Test[]    findBy(array $criteria, array $orderBy = null, int $limit = null)
 */
class TestRepository extends ObjectManager
{
    public function __construct()
    {
        parent::__construct(Test::class);
    }

    public function save(Test $entity): void
    {
        $this->persist($entity);
    }

    public function delete(Test $entity): void
    {
        $this->remove($entity);
    }

    public function queryBuilderTestSelect(): array
    {
        // TODO: TEST
        $qb = $this->databaseManager->createQueryBuilder();
    }
}
