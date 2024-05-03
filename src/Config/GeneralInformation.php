<?php

declare(strict_types=1);

namespace Knowolo\Config;

/**
 * @api
 */
readonly class GeneralInformation
{
    private string|null $title;
    private string|null $summary;
    private string|null $homepage;
    private string|null $license;
    private string|null $authors;

    public function __construct(
        string|null $title = null,
        string|null $summary = null,
        string|null $homepage = null,
        string|null $license = null,
        string|null $authors = null,
    ) {
        $this->title = $title;
        $this->summary = $summary;
        $this->homepage = $homepage;
        $this->license = $license;
        $this->authors = $authors;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getSummary(): string|null
    {
        return $this->summary;
    }

    public function getHomepage(): string|null
    {
        return $this->homepage;
    }

    public function getLicense(): string|null
    {
        return $this->license;
    }

    public function getAuthors(): string|null
    {
        return $this->authors;
    }
}
