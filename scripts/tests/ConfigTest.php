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
        ]));

        $this->assertTrue($config->getIncludeTermInformation());
        $this->assertFalse($config->getIncludeClassInformation());
        $this->assertEquals(['fr', 'it'], $config->getPreferedLanguages());
        $this->assertEquals('\\Knowolo\\DefaultImplementation\\KnowledgeModule', $config->getPhpModuleClasspath());
    }

    public function testInitNoJson(): void
    {
        $config = new Config();

        $this->assertTrue($config->getIncludeTermInformation());
        $this->assertTrue($config->getIncludeClassInformation());
        $this->assertEquals([], $config->getPreferedLanguages());
        $this->assertEquals('\\Knowolo\\DefaultImplementation\\KnowledgeModule', $config->getPhpModuleClasspath());
    }
}
