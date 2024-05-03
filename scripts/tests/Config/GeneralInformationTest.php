<?php

namespace Tests\GeneralInformation;

use Knowolo\Config\GeneralInformation;
use Test\TestCase;

class GeneralInformationTest extends TestCase
{
    public function testInit(): void
    {
        $subjectUnderTest = new GeneralInformation(
            'title1',
            'summary1',
            'homepage1',
            'license1',
            'authors1',
        );

        $this->assertEquals('title1', $subjectUnderTest->getTitle());
        $this->assertEquals('summary1', $subjectUnderTest->getSummary());
        $this->assertEquals('homepage1', $subjectUnderTest->getHomepage());
        $this->assertEquals('license1', $subjectUnderTest->getLicense());
        $this->assertEquals('authors1', $subjectUnderTest->getAuthors());
    }
}
