<?php

namespace Tests\Generator;

use Knowolo\Config;
use Knowolo\Generator\SerializedPhpCodeGenerator;
use quickRdf\DataFactory;
use Test\TestCase;

class SerializedPhpCodeGeneratorTest extends TestCase
{
    public function testAlignClassTitlesFalse(): void
    {
        $serializedPhpCodeGenerator = new SerializedPhpCodeGenerator(new DataFactory());

        $config = new Config(json_encode([
            'general-information' => [
                'title' => 'title1',
                'summary' => 'summary1',
                'license' => 'license1',
                'homepage' => 'homepage1',
                'authors' => 'authors1'
            ],
            'include-term-information' => true,
            'include-class-information' => true,
            'prefered-languages' => ['fr', 'it'],
            'php-module-classpath' => '\\Knowolo\\DefaultImplementation\\KnowledgeModule',
            'php-namespace' => '\\KnowledgeModule',
            'custom-label-properties-for-classes' => ['rdfs:label'],
            'compress-class-ids' => true,
            'compress-term-ids' => true,
        ]));

        $result = $serializedPhpCodeGenerator->generateFileData(
            __DIR__.'/../../test/files/hierarchy.ttl',
            $config
        );

        $this->assertTrue(str_contains($result, 'title1'));
        $this->assertTrue(str_contains($result, 'summary1'));
        $this->assertTrue(str_contains($result, 'homepage1'));
        $this->assertTrue(str_contains($result, 'license1'));
        $this->assertTrue(str_contains($result, 'authors1'));
    }
}
