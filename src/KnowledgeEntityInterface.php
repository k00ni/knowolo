<?php

declare(strict_types=1);

namespace Knowolo;

/**
 * A knowledge entity usually represents a term or class or class property.
 *
 * @api
 */
interface KnowledgeEntityInterface
{
    /**
     * A name (or title or label) of an entity. It must not be empty.
     *
     * @param string|null       $language Language of the name, usually 2 or 3 characters, such as en.
     *                          If its null or not set, then the implementation determines the language.
     *
     * @return non-empty-string
     */
    public function getTitle(string|null $language = null): string;

    /**
     * Get the ID of the entity. For instance an URL, URI or IRI.
     *
     * It is also allowed to return the name itself, if no one cares about the ID in general.
     *
     * @return non-empty-string
     */
    public function getId(): string;

    /**
     * Generates PHP code which represents a new statement, something like:
     *
     *     new KnowledgeEntity(['en' => 'too title'], 'http://id/1')
     *
     * @return non-empty-string
     */
    public function asPhpCodeNew(): string;
}
