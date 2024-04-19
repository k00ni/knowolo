<?php

namespace Tests;

use Knowolo\Config;
use Test\TestCase;

class ConfigTest extends TestCase
{
    public function testInit(): void
    {
        $config = new Config(json_encode([
            'include-term-information' => true,
            'include-class-information' => false,
            'prefered-languages' => ['fr', 'it'],
            'php-module-classpath' => '\\Knowolo\\DefaultImplementation\\KnowledgeModule',
            'php-namespace' => '\\KnowledgeModule',
            'custom-label-properties-for-classes' => ['rdfs:label'],
            'compress-class-ids' => true,
            'compress-term-ids' => true,
        ]));

        $this->assertTrue($config->getIncludeTermInformation());
        $this->assertFalse($config->getIncludeClassInformation());
        $this->assertEquals(['fr', 'it'], $config->getPreferedLanguages());
        $this->assertEquals('\\Knowolo\\DefaultImplementation\\KnowledgeModule', $config->getPhpModuleClasspath());
        $this->assertEquals(['rdfs:label'], $config->getCustomLabelPropertiesForClasses());
        $this->assertTrue($config->getCompressClassIds());
        $this->assertTrue($config->getCompressTermIds());
    }

    /**
     * Test default settings
     */
    public function testInitNoJson(): void
    {
        $config = new Config();

        $this->assertTrue($config->getIncludeTermInformation());
        $this->assertTrue($config->getIncludeClassInformation());
        $this->assertEquals([], $config->getPreferedLanguages());
        $this->assertEquals('\\Knowolo\\DefaultImplementation\\KnowledgeModule', $config->getPhpModuleClasspath());
        $this->assertEquals([], $config->getCustomLabelPropertiesForClasses());
        $this->assertFalse($config->getCompressClassIds());
        $this->assertFalse($config->getCompressTermIds());
    }
}
