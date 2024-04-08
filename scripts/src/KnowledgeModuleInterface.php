<?php

declare(strict_types=1);

namespace Knowolo;

/**
 * @api
 */
interface KnowledgeModuleInterface
{
    /**
     * Taxonomy level ---------------------------------------------------------
     */

    /**
     * Returns a list of all broader terms, if available.
     *
     * @param non-empty-string|\Knowolo\KnowledgeEntityInterface $term
     * @param string|null            $language   Language of the description,
     *                                           usually 2 or 3 characters (e.g. EN).
     */
    public function getBroaderTerms(
        string|KnowledgeEntityInterface $term,
        string|null $language
    ): KnowledgeEntityListInterface;

    /**
     * Returns a list of all narrower terms, if available.
     *
     * @param non-empty-string|\Knowolo\KnowledgeEntityInterface $term
     * @param string|null            $language   Language of the description,
     *                                           usually 2 or 3 characters (e.g. EN).
     */
    public function getNarrowerTerms(
        string|KnowledgeEntityInterface $term,
        string|null $language
    ): KnowledgeEntityListInterface;

    /**
     * Returns a list of all known terms.
     */
    public function getTerms(): KnowledgeEntityListInterface;

    /**
     * Ontology level ---------------------------------------------------------
     */

    /**
     * Returns a list of all available classes.
     */
    public function getClasses(): KnowledgeEntityListInterface;

    /**
     * Returns a list of all available super classes for a given class.
     *
     * @param non-empty-string|\Knowolo\KnowledgeEntityInterface $class
     */
    public function getSuperClasses(KnowledgeEntityInterface|string $class): KnowledgeEntityListInterface;

    /**
     * Returns a list of all available sub classes for a given class.
     *
     * @param non-empty-string|\Knowolo\KnowledgeEntityInterface $class
     */
    public function getSubClasses(KnowledgeEntityInterface|string $class): KnowledgeEntityListInterface;
}
