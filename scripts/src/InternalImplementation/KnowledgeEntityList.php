<?php

declare(strict_types=1);

namespace Knowolo\InternalImplementation;

use Knowolo\KnowledgeEntityInterface;
use Knowolo\KnowledgeEntityListInterface;

/**
 * @internal This implementation is only meant to be used inside this project.
 *           If used externally, keep it mind it may change without further notice.
 */
class KnowledgeEntityList implements KnowledgeEntityListInterface
{
    /**
     * @var array<\Knowolo\KnowledgeEntityInterface>
     */
    private array $entities = [];

    /**
     * @var array<non-empty-string,\Knowolo\KnowledgeEntityListInterface>
     */
    private array $entitiesOfHigherOrder = [];

    /**
     * @var array<non-empty-string,\Knowolo\KnowledgeEntityListInterface>
     */
    private array $entitiesOfLowerOrder = [];

    private int $position = 0;
    private bool $reverse = false;

    /**
     * @param list<\Knowolo\KnowledgeEntityInterface> $entities
     */
    public function __construct(array $entities = [])
    {
        $this->addEntities($entities);
    }

    public function add(KnowledgeEntityInterface $entity): void
    {
        if ($this->getEntityById($entity->getId()) instanceof KnowledgeEntityInterface) {
            // already in list, so ignore
        } else {
            $this->entities[] = $entity;
        }
    }

    /**
     * @param list<\Knowolo\KnowledgeEntityInterface> $entities
     */
    public function addEntities(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->add($entity);
        }
    }

    public function rewind(): void
    {
        $this->position = $this->reverse ? count($this->entities) - 1 : 0;
    }

    public function current(): KnowledgeEntityInterface
    {
        return $this->entities[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position = $this->position + ($this->reverse ? -1 : 1);
    }

    public function valid(): bool
    {
        return isset($this->entities[$this->position]);
    }

    public function asArray(): array
    {
        return $this->entities;
    }

    public function asListOfIds(): iterable
    {
        $result = [];

        foreach ($this->entities as $entity) {
            $result[] = $entity->getId();
        }

        return $result;
    }

    public function asListOfNames(string|null $language = null): iterable
    {
        $result = [];

        foreach ($this->entities as $entity) {
            $result[] = $entity->getName($language);
        }

        return $result;
    }

    public function getEntityById(string $id): KnowledgeEntityInterface|null
    {
        return $this->getEntityByIdInternal($id, $this->entities);
    }

    public function getEntityByName(string $name, string|null $language): KnowledgeEntityInterface|null
    {
        foreach ($this->entities as $entity) {
            if ($entity->getName($language) == $name) {
                return $entity;
            }
        }

        return null;
    }

    public function addRelatedEntitiesOfHigherOrder(
        KnowledgeEntityInterface $entity,
        iterable $entitiesOfHigherOrder
    ): void {
        $this->add($entity);

        if (false === isset($this->entitiesOfHigherOrder[$entity->getId()])) {
            $this->entitiesOfHigherOrder[$entity->getId()] = new self();
        }

        foreach ($entitiesOfHigherOrder as $entityOfHigherOrder) {
            $this->entitiesOfHigherOrder[$entity->getId()]->add($entityOfHigherOrder);
            $this->add($entityOfHigherOrder);
        }
    }

    public function getRelatedEntitiesOfHigherOrder(KnowledgeEntityInterface $entity): KnowledgeEntityListInterface
    {
        $result = new self();

        // entries of higher order
        if (isset($this->entitiesOfHigherOrder[$entity->getId()])) {
            $result->addEntities($this->entitiesOfHigherOrder[$entity->getId()]);
        }

        return $result;
    }

    public function addRelatedEntitiesOfLowerOrder(
        KnowledgeEntityInterface $entity,
        iterable $entitiesOfLowerOrder
    ): void {
        $this->add($entity);

        if (false === isset($this->entitiesOfHigherOrder[$entity->getId()])) {
            $this->entitiesOfLowerOrder[$entity->getId()] = new self();
        }

        foreach ($entitiesOfLowerOrder as $entityOfLowerOrder) {
            $this->entitiesOfLowerOrder[$entity->getId()]->add($entityOfLowerOrder);
            $this->add($entityOfLowerOrder);
        }
    }

    public function getRelatedEntitiesOfLowerOrder(KnowledgeEntityInterface $entity): KnowledgeEntityListInterface
    {
        $result = new self();

        // entries of lower order
        if (isset($this->entitiesOfLowerOrder[$entity->getId()])) {
            $result->addEntities($this->entitiesOfLowerOrder[$entity->getId()]);
        }

        return $result;
    }

    /**
     * @param non-empty-string $id
     * @param list<\Knowolo\KnowledgeEntityInterface> $list
     */
    private function getEntityByIdInternal(string $id, iterable $list): KnowledgeEntityInterface|null
    {
        foreach ($list as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }

        return null;
    }
}
