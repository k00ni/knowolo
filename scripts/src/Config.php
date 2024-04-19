<?php

declare(strict_types=1);

namespace Knowolo;

/**
 * Represents a config file
 */
readonly class Config
{
    private bool $compressClassIds;
    private bool $compressTermIds;

    /**
     * @var array<non-empty-string> List of label properties (shortend URIs, e.g. rdfs:label)
     */
    private array $customLabelPropertiesForClasses;

    private bool $includeTermInformation;
    private bool $includeClassInformation;

    /**
     * @var array<non-empty-string>
     */
    private array $preferedLanguages;

    /**
     * @var class-string
     */
    private string $phpModuleClasspath;

    /**
     * @param string|null $json
     *
     * @throws \Knowolo\Exception if JSON parsing failed
     * @throws \Knowolo\Exception if a field is invalid
     */
    public function __construct(string|null $json = null)
    {
        if (isEmpty($json)) {
            $configArr = [];
        } else {
            /** @var array<string, mixed> */
            $configArr = json_decode((string) $json, true);
            if (0 < json_last_error()) {
                throw new Exception('JSON parsing failed: '.json_last_error_msg());
            }
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
                throw new Exception('Field custom-label-properties-for-classes must not contain an empty-string entry');
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
                throw new Exception('Field prefered-language contains an empty-string entry');
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
     * @return array<non-empty-string>
     */
    public function getCustomLabelPropertiesForClasses(): array
    {
        return $this->customLabelPropertiesForClasses;
    }

    public function getCompressTermIds(): bool
    {
        return $this->compressTermIds;
    }

    public function getCompressClassIds(): bool
    {
        return $this->compressClassIds;
    }
}
