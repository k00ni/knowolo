<?php

declare(strict_types=1);

namespace Knowolo;

use Knowolo\Config\GeneralInformation;
use Knowolo\Exception\KnowoloException;

/**
 * Represents a config file.
 *
 * @api
 */
class Config
{
    /**
     * If true, class titles are being changed so each word starts with an uppercase letter
     * followed by lowercase letters.
     */
    private readonly bool $alignClassTitles;
    private readonly bool $compressClassIds;
    private readonly bool $compressTermIds;

    /**
     * @var array<non-empty-string> List of label properties (shortend URIs, e.g. rdfs:label)
     */
    private readonly array $customLabelPropertiesForClasses;

    private GeneralInformation $generalInformation;

    private readonly bool $includeTermInformation;
    private readonly bool $includeClassInformation;

    /**
     * @var array<non-empty-string>
     */
    private readonly array $preferedLanguages;

    /**
     * @var class-string
     */
    private readonly string $phpModuleClasspath;

    /**
     * @var string|null
     */
    private readonly string|null $sortAllEntitiesByTitleAscUsingLanguage;

    /**
     * @param string|null $json
     *
     * @throws \Knowolo\Exception\KnowoloException if JSON parsing failed
     * @throws \Knowolo\Exception\KnowoloException if a field is invalid
     */
    public function __construct(string|null $json = null)
    {
        if (isEmpty($json)) {
            $configArr = [];
        } else {
            /** @var array<string, mixed> */
            $configArr = json_decode((string) $json, true);
            if (0 < json_last_error()) {
                throw new KnowoloException('JSON parsing failed: '.json_last_error_msg());
            }
        }

        // general-information
        if (isset($configArr['general-information']) && is_array($configArr['general-information'])) {
            $gi = new GeneralInformation(
                $configArr['general-information']['title'] ?? null,
                $configArr['general-information']['summary'] ?? null,
                $configArr['general-information']['homepage'] ?? null,
                $configArr['general-information']['license'] ?? null,
                $configArr['general-information']['authors'] ?? null,
            );
        } else {
            $gi = new GeneralInformation();
        }

        $this->generalInformation = $gi;

        // align-class-titles
        if (isset($configArr['align-class-titles']) && is_bool($configArr['align-class-titles'])) {
            $this->alignClassTitles = $configArr['align-class-titles'];
        } else {
            $this->alignClassTitles = false;
        }

        // compress-class-ids
        if (isset($configArr['compress-class-ids']) && is_bool($configArr['compress-class-ids'])) {
            $this->compressClassIds = $configArr['compress-class-ids'];
        } else {
            $this->compressClassIds = false;
        }

        // compress-term-ids
        if (isset($configArr['compress-term-ids']) && is_bool($configArr['compress-term-ids'])) {
            $this->compressTermIds = $configArr['compress-term-ids'];
        } else {
            $this->compressTermIds = false;
        }

        // custom-label-properties-for-classes
        if (
            isset($configArr['custom-label-properties-for-classes'])
            && is_array($configArr['custom-label-properties-for-classes'])
        ) {
            $this->customLabelPropertiesForClasses = $configArr['custom-label-properties-for-classes'];
        } else {
            $this->customLabelPropertiesForClasses = [];
        }
        foreach ($this->customLabelPropertiesForClasses as $label) {
            if (isEmpty($label)) {
                throw new KnowoloException('Field custom-label-properties-for-classes must not contain an empty-string entry');
            }
        }

        // include-term-information
        if (isset($configArr['include-term-information']) && is_bool($configArr['include-term-information'])) {
            $this->includeTermInformation = $configArr['include-term-information'];
        } else {
            $this->includeTermInformation = true;
        }

        // include-class-information
        if (isset($configArr['include-class-information']) && is_bool($configArr['include-class-information'])) {
            $this->includeClassInformation = $configArr['include-class-information'];
        } else {
            $this->includeClassInformation = true;
        }

        // prefered-languages
        if (isset($configArr['prefered-languages']) && is_array($configArr['prefered-languages'])) {
            $this->preferedLanguages = $configArr['prefered-languages'];
        } else {
            $this->preferedLanguages = [];
        }
        foreach ($this->preferedLanguages as $language) {
            if (isEmpty($language)) {
                throw new KnowoloException('Field prefered-language contains an empty-string entry');
            }
        }

        // php-module-classpath
        if (
            isset($configArr['php-module-classpath'])
            && is_string($configArr['php-module-classpath'])
            && class_exists($configArr['php-module-classpath'])
        ) {
            $this->phpModuleClasspath = $configArr['php-module-classpath'];
        } else {
            $this->phpModuleClasspath = '\\Knowolo\\DefaultImplementation\\KnowledgeModule';
        }

        // sort-all-entities-by-title-asc-using-language
        if (
            isset($configArr['sort-all-entities-by-title-asc-using-language'])
            && (
                is_string($configArr['sort-all-entities-by-title-asc-using-language'])
                || null == $configArr['sort-all-entities-by-title-asc-using-language']
            )
        ) {
            // PHPStan acrobatics (casting fails; (string|null) does not accept array|float|int|string|false)
            /** @var string|null */
            $sortAll = $configArr['sort-all-entities-by-title-asc-using-language'];
            $this->sortAllEntitiesByTitleAscUsingLanguage = $sortAll;
        } else {
            $this->sortAllEntitiesByTitleAscUsingLanguage = null;
        }
    }

    public function getAlignClassTitles(): bool
    {
        return $this->alignClassTitles;
    }

    public function getCompressTermIds(): bool
    {
        return $this->compressTermIds;
    }

    public function getCompressClassIds(): bool
    {
        return $this->compressClassIds;
    }

    /**
     * @return array<non-empty-string>
     */
    public function getCustomLabelPropertiesForClasses(): array
    {
        return $this->customLabelPropertiesForClasses;
    }

    public function getIncludeTermInformation(): bool
    {
        return $this->includeTermInformation;
    }

    public function getIncludeClassInformation(): bool
    {
        return $this->includeClassInformation;
    }

    /**
     * @return class-string
     */
    public function getPhpModuleClasspath(): string
    {
        return $this->phpModuleClasspath;
    }

    /**
     * @return array<non-empty-string>
     */
    public function getPreferedLanguages(): array
    {
        return $this->preferedLanguages;
    }

    /**
     * @return string|null
     */
    public function getSortAllEntitiesByTitleAscUsingLanguage(): string|null
    {
        return $this->sortAllEntitiesByTitleAscUsingLanguage;
    }

    public function getGeneralInformation(): GeneralInformation
    {
        return $this->generalInformation;
    }
}
