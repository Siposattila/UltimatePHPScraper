<?php

namespace App\ObjectManager;

/**
 * @template T
 * @template-extends \App\Entity\BaseEntity
 */
class ObjectManager
{
    /**
     * @var T[] $objects
     */
    private $objects;

    /**
     * @return T[]
     */
    public function all()
    {
        return $this->objects;
    }

    /**
     * @return T|null
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
     * @return T[]
     */
    public function findBy(array $criteria)
    {
        // TODO: implement
        return [];
    }

    /**
     * @return T
     */
    public function findOneBy(array $criteria)
    {
        // TODO: implement
    }

    /**
     * @param T $entity
     * @return void
     */
    public function persist($entity)
    {
        $this->objects[] = $entity;
    }

    /**
     * @param T $entity
     * @return void
     */
    public function remove($entity): void
    {
        $i = 0;
        while($i < count($this->objects) && $this->objects[$i]->getId() != $entity->getId()) {
            $i++;
        }

        if ($i < count($this->objects)) {
            unset($this->objects[$i]);
        }
    }
}
