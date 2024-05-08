<?php

namespace Tests\DefaultImplementation;

use Knowolo\LanguageRelatedStringHandler;
use Test\TestCase;

class LanguageRelatedStringHandlerTest extends TestCase
{
    public function testCreationWithInvalidLangToNames1(): void
    {
        $this->expectExceptionMessage('titlePerLanguage has no elements');

        new LanguageRelatedStringHandler([], 'id1');
    }

    public function testCreationWithInvalidLangToNames2(): void
    {
        $this->expectExceptionMessage('titlePerLanguage has one element, which is empty');

        new LanguageRelatedStringHandler(['' => ''], 'id1');
    }

    public function testgetTitleNoLanguageTag(): void
    {
        $sut = new LanguageRelatedStringHandler(['' => 'foo']);

        $this->assertEquals('foo', $sut->getString());
        $this->assertEquals('foo', $sut->getString(''));
    }
}
