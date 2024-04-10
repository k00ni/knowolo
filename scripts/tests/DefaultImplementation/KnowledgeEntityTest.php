<?php

namespace Tests\DefaultImplementation;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Test\TestCase;

class KnowledgeEntityTest extends TestCase
{
    public function testCreationWithInvalidLangToNames1(): void
    {
        $this->expectExceptionMessage('namePerLanguage has no elements');

        new KnowledgeEntity([], 'id1');
    }

    public function testCreationWithInvalidLangToNames2(): void
    {
        $this->expectExceptionMessage('namePerLanguage has one element, which is empty');

        new KnowledgeEntity(['' => ''], 'id1');
    }

    public function testGetNameNoLanguageTag(): void
    {
        $sut = new KnowledgeEntity(['' => 'foo'], 'id1');

        $this->assertEquals('foo', $sut->getName());
        $this->assertEquals('foo', $sut->getName(''));
    }

    public function testAsPhpCodeNew(): void
    {
        // default
        $sut = new KnowledgeEntity(['de' => 'foo', 'en' => 'bar'], 'id1');

        $this->assertEquals(
            "new \Knowolo\DefaultImplementation\KnowledgeEntity(['de' => 'foo', 'en' => 'bar'], 'id1')",
            $sut->asPhpCodeNew()
        );

        // with slashes
        $sut = new KnowledgeEntity(['de' => "foo's bar", 'en' => 'bar "baz"'], 'id1');

        $this->assertEquals(
            "new \Knowolo\DefaultImplementation\KnowledgeEntity(['de' => 'foo\'s bar', 'en' => 'bar \\\"baz\\\"'], 'id1')",
            $sut->asPhpCodeNew()
        );
    }
}
