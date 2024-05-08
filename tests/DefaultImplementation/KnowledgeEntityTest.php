<?php

namespace Tests\DefaultImplementation;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Knowolo\LanguageRelatedStringHandler;
use Test\TestCase;

class KnowledgeEntityTest extends TestCase
{
    public function testAsPhpCodeNew(): void
    {
        // default
        $sut = new KnowledgeEntity(new LanguageRelatedStringHandler(['de' => 'foo', 'en' => 'bar']), 'id1');

        $this->assertEquals(
            "new \Knowolo\DefaultImplementation\KnowledgeEntity(['de' => 'foo', 'en' => 'bar'], 'id1')",
            $sut->asPhpCodeNew()
        );

        // with slashes
        $sut = new KnowledgeEntity(new LanguageRelatedStringHandler(['de' => "foo's bar", 'en' => 'bar "baz"']), 'id1');

        $this->assertEquals(
            "new \Knowolo\DefaultImplementation\KnowledgeEntity(['de' => 'foo\'s bar', 'en' => 'bar \\\"baz\\\"'], 'id1')",
            $sut->asPhpCodeNew()
        );
    }
}
