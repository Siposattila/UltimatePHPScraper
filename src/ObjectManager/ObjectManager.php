<?php

namespace App\ObjectManager;

use App\Entity\BaseEntity;

class ObjectManager
{
    /** @var \App\Entity\BaseEntity[] $objects */
    private array $objects;

    protected function find(int $id): mixed
    {
        // FIXME: if the given id is smaller then the current id then stop
        $i = 0;
        while($i < count($this->objects) && $this->objects[$i]->getId() != $id) {
            $i++;
        }

        if ($i < count($this->objects)) {
            return $this->objects[$i];
        }

        return false;
    }

    protected function findBy(array $criteria): array
    {
        // return false;
        return [];
    }

    protected function findOneBy(): mixed
    {
        // TODO: implement
    }

    protected function persist(BaseEntity $entity): void
    {
        $this->objects[] = $entity;
    }

    protected function remove(BaseEntity $entity): void
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
