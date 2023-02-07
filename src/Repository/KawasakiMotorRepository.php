<?php

namespace App\Repository;

use App\Entity\KawasakiMotor;
use App\ObjectManager\ObjectManager;

/**
 * @extends ObjectManager<KawasakiMotor>
 *
 * @method KawasakiMotor|null find(int $id)
 * @method KawasakiMotor|null findOneBy(array $criteria, array $orderBy = null)
 * @method KawasakiMotor[]    findAll(array $criteria, array $orderBy = null)
 * @method KawasakiMotor[]    findBy(array $criteria, array $orderBy = null, int $limit = null)
 */
class KawasakiMotorRepository extends ObjectManager
{
    public function __construct()
    {
        parent::__construct(KawasakiMotor::class);
    }

    public function save(KawasakiMotor $entity): void
    {
        $this->persist($entity);
    }

    public function delete(KawasakiMotor $entity): void
    {
        $this->remove($entity);
    }
}
