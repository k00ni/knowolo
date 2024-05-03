<?php

declare(strict_types=1);

namespace Knowolo\DefaultImplementation;

use Knowolo\KnowledgeEntityInterface;
use Knowolo\KnowledgeEntityListInterface;

/**
 * @api
 */
class KnowledgeEntityList implements KnowledgeEntityListInterface
{
    /**
     * @var array<\Knowolo\KnowledgeEntityInterface>
     */
    private array $entities = [];

    /**
     * @var non-negative-int
     */
    private int $entityCount = 0;

    /**
     * @var array<non-empty-string,\Knowolo\KnowledgeEntityListInterface>
     */
    private array $entitiesOfHigherOrder = [];

    /**
     * @var array<non-empty-string,\Knowolo\KnowledgeEntityListInterface>
     */
    private array $entitiesOfLowerOrder = [];

    /**
     * @var non-negative-int
     */
    private int $position = 0;

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
            ++$this->entityCount;
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

    /**
     * @return non-negative-int
     */
    public function count(): int
    {
        return $this->entityCount;
    }

    public function rewind(): void
    {
        $this->position = 0;
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
        ++$this->position;
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

    public function asListOfTitles(string|null $language = null): iterable
    {
        $result = [];

        foreach ($this->entities as $entity) {
            $result[] = $entity->getTitle($language);
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
            if ($entity->getTitle($language) == $name) {
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

            // add entities for lower order; this allows users to later ask for both ways:
            // get entities of higher or lower order
            $id = $entityOfHigherOrder->getId();
            if (false === isset($this->entitiesOfLowerOrder[$id])) {
                $this->entitiesOfLowerOrder[$id] = new self();
            }
            $this->entitiesOfLowerOrder[$id]->add($entity);
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

        if (false === isset($this->entitiesOfLowerOrder[$entity->getId()])) {
            $this->entitiesOfLowerOrder[$entity->getId()] = new self();
        }

        foreach ($entitiesOfLowerOrder as $entityOfLowerOrder) {
            $this->entitiesOfLowerOrder[$entity->getId()]->add($entityOfLowerOrder);
            $this->add($entityOfLowerOrder);

            // add entities for higher order; this allows users to later ask for both ways:
            // get entities of higher or lower order
            $id = $entityOfLowerOrder->getId();
            if (false === isset($this->entitiesOfHigherOrder[$id])) {
                $this->entitiesOfHigherOrder[$id] = new self();
            }
            $this->entitiesOfHigherOrder[$id]->add($entity);
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

    public function sortByTitleAscending(string|null $language = null): void
    {
        // sort entities list itself
        usort($this->entities, function ($a, $b) use ($language) {
            return $a->getTitle($language) < $b->getTitle($language) ? -1 : 1;
        });

        // sort all sub lists in entitiesOfLowerOrder and ...
        foreach (array_keys($this->entitiesOfLowerOrder) as $id) {
            $this->entitiesOfLowerOrder[$id]->sortByTitleAscending($language);
        }
        // ... entitiesOfHigherOrder
        foreach (array_keys($this->entitiesOfHigherOrder) as $id) {
            $this->entitiesOfHigherOrder[$id]->sortByTitleAscending($language);
        }
    }
}
