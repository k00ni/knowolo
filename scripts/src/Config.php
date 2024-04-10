<?php

declare(strict_types=1);

namespace Knowolo;

/**
 * Represents a config file
 */
readonly class Config
{
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
}
