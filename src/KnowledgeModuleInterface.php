<?php

declare(strict_types=1);

namespace Knowolo;

use Stringable;

/**
 * @api
 */
interface KnowledgeModuleInterface
{
    public function __construct(
        Config $config,
        KnowledgeEntityListInterface|null $termsInformation = null,
        KnowledgeEntityListInterface|null $classInformation = null
    );

    /**
     * General information about the knowledge module -------------------------
     */
    public function getTitle(): Stringable|string|null;
    public function getAuthors(): Stringable|string|null;
    public function getSummary(): Stringable|string|null;

    /**
     * A homepage about the knowledge module and/or the ontology its derived from.
     *
     * Typically its the project page or a Github/GitLab repository link.
     */
    public function getHomepage(): Stringable|string|null;

    /**
     * License of the whole knowledge module.
     *
     * If it is derived from an actual ontology, the license should be the same for
     * both, the ontology as well as the knowledge module.
     *
     * The function should return either a standardized short version of the license,
     * such as "CC-BY 4.0" for http://creativecommons.org/licenses/by/4.0/ or an URL
     * to a document which contains license information, such as
     * "http://creativecommons.org/licenses/by/4.0/".
     */
    public function getLicense(): Stringable|string|null;

    /**
     * Taxonomy level ---------------------------------------------------------
     */

    /**
     * Returns a list of all broader terms, if available.
     *
     * @param non-empty-string|\Knowolo\KnowledgeEntityInterface $term
     * @param string|null                                        $language Language of the description,
     *                                                                     usually 2 or 3 characters (e.g. EN).
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
