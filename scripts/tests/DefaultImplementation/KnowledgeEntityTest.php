<?php

namespace Tests\DefaultImplementation;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Test\TestCase;

class KnowledgeEntityTest extends TestCase
{
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
