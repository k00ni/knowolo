<?php

declare(strict_types=1);

namespace Knowolo\DefaultImplementation;

use Knowolo\Config;
use Knowolo\Exception;
use Knowolo\KnowledgeEntityInterface;
use Knowolo\KnowledgeEntityListInterface;
use Knowolo\KnowledgeModuleInterface;
use Stringable;

/**
 * @api
 */
class KnowledgeModule implements KnowledgeModuleInterface
{
    private Config $config;

    private KnowledgeEntityListInterface $classInformation;

    private KnowledgeEntityListInterface $termInformation;

    public function __construct(
        Config $config,
        KnowledgeEntityListInterface $termInformation = null,
        KnowledgeEntityListInterface $classInformation = null
    ) {
        $this->config = $config;
        $this->classInformation = $classInformation ?? new KnowledgeEntityList();
        $this->termInformation = $termInformation ?? new KnowledgeEntityList();
    }

    public function getTitle(): string|null
    {
        return $this->config->getGeneralInformation()->getTitle();
    }

    public function getSummary(): string|null
    {
        return $this->config->getGeneralInformation()->getSummary();
    }

    public function getHomepage(): string|null
    {
        return $this->config->getGeneralInformation()->getHomepage();
    }

    public function getLicense(): Stringable|string|null
    {
        return $this->config->getGeneralInformation()->getLicense();
    }

    public function getAuthors(): Stringable|string|null
    {
        return $this->config->getGeneralInformation()->getAuthors();
    }

    /**
     * @throws \Knowolo\Exception if a term was not found
     */
    public function getBroaderTerms(
        string|KnowledgeEntityInterface $term,
        string|null $language = null
    ): KnowledgeEntityListInterface {
        if (is_string($term)) {
            $term = $this->termInformation->getEntityByName($term, $language);
        }

        $termIsNotValidInstance = false === $term instanceof KnowledgeEntityInterface;
        if ($termIsNotValidInstance) {
            throw new Exception('No term found with name: ' . $term . ' in language: ' . $language);
        }

        return $this->termInformation->getRelatedEntitiesOfHigherOrder($term);
    }

    /**
     * @throws \Knowolo\Exception if a term was not found
     */
    public function getNarrowerTerms(
        string|KnowledgeEntityInterface $term,
        string|null $language = null
    ): KnowledgeEntityListInterface {
        if (is_string($term)) {
            $term = $this->termInformation->getEntityByName($term, $language);
        }

        $termIsNotValidInstance = false === $term instanceof KnowledgeEntityInterface;
        if ($termIsNotValidInstance) {
            throw new Exception('No term found with name: ' . $term . ' in language: ' . $language);
        }

        return $this->termInformation->getRelatedEntitiesOfLowerOrder($term);
    }

    public function getTerms(): KnowledgeEntityListInterface
    {
        return $this->termInformation;
    }

    /**
     * @throws \Knowolo\Exception if a class was not found
     */
    public function getSuperClasses(
        string|KnowledgeEntityInterface $class,
        string|null $language = null
    ): KnowledgeEntityListInterface {
        if (is_string($class)) {
            $class = $this->classInformation->getEntityByName($class, $language);
        }

        $classIsNotValidInstance = false === $class instanceof KnowledgeEntityInterface;
        if ($classIsNotValidInstance) {
            throw new Exception('No class found with name: ' . $class . ' in language: ' . $language);
        }

        return $this->classInformation->getRelatedEntitiesOfHigherOrder($class);
    }

    /**
     * @throws \Knowolo\Exception if a class was not found
     */
    public function getSubClasses(
        string|KnowledgeEntityInterface $class,
        string|null $language = null
    ): KnowledgeEntityListInterface {
        if (is_string($class)) {
            $class = $this->classInformation->getEntityByName($class, $language);
        }

        $classIsNotValidInstance = false === $class instanceof KnowledgeEntityInterface;
        if ($classIsNotValidInstance) {
            throw new Exception('No class found with name: ' . $class . ' in language: ' . $language);
        }

        return $this->classInformation->getRelatedEntitiesOfLowerOrder($class);
    }

    public function getClasses(): KnowledgeEntityListInterface
    {
        return $this->classInformation;
    }
}
