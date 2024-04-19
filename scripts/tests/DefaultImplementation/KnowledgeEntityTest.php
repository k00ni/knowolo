<?php

namespace Tests\DefaultImplementation;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Test\TestCase;

class KnowledgeEntityTest extends TestCase
{
    public function testCreationWithInvalidLangToNames1(): void
    {
        $this->expectExceptionMessage('titlePerLanguage has no elements');

        new KnowledgeEntity([], 'id1');
    }

    public function testCreationWithInvalidLangToNames2(): void
    {
        $this->expectExceptionMessage('titlePerLanguage has one element, which is empty');

        new KnowledgeEntity(['' => ''], 'id1');
    }

    public function testgetTitleNoLanguageTag(): void
    {
        $sut = new KnowledgeEntity(['' => 'foo'], 'id1');

        $this->assertEquals('foo', $sut->getTitle());
        $this->assertEquals('foo', $sut->getTitle(''));
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
