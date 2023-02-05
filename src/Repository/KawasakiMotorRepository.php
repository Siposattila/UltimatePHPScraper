<?php

namespace App\Repository;

use App\Entity\KawasakiMotor;
use App\ObjectManager\ObjectManager;

/**
 * @template-extends ObjectManager<\App\Entity\KawasakiMotor>
 */
class KawasakiMotorRepository extends ObjectManager
{
    public function save(KawasakiMotor $entity): void
    {
        $this->persist($entity);
    }

    public function delete(KawasakiMotor $entity): void
    {
        $this->remove($entity);
    }
}
