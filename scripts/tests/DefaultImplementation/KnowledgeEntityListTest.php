<?php

namespace Tests\DefaultImplementation;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Knowolo\DefaultImplementation\KnowledgeEntityList;
use Knowolo\KnowledgeEntityListInterface;
use Test\TestCase;

class KnowledgeEntityListTest extends TestCase
{
    private function getSubjectUnderTest(array $entries = []): KnowledgeEntityListInterface
    {
        return new KnowledgeEntityList($entries);
    }

    public function testEntitysIndirectlyAdded1(): void
    {
        $sut = $this->getSubjectUnderTest();

        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $sut->addRelatedEntitiesOfHigherOrder($oak, [$tree]);

        $this->assertEquals([$oak, $tree], $sut->asArray());
    }

    public function testEntitysIndirectlyAdded2(): void
    {
        $sut = $this->getSubjectUnderTest();

        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $sut->addRelatedEntitiesOfLowerOrder($tree, [$oak]);

        $this->assertEquals([$tree, $oak], $sut->asArray());
    }

    public function testGetRelatedEntitiesOfHigherOrderForEntity(): void
    {
        $sut = $this->getSubjectUnderTest();

        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $sut->addRelatedEntitiesOfHigherOrder($oak, [$tree]);

        $this->assertEquals([$tree], $sut->getRelatedEntitiesOfHigherOrder($oak)->asArray());
    }

    public function testGetRelatedEntitiesOfLowerOrder(): void
    {
        $sut = $this->getSubjectUnderTest();

        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $sut->addRelatedEntitiesOfLowerOrder($tree, [$oak]);

        $this->assertEquals([$oak], $sut->getRelatedEntitiesOfLowerOrder($tree)->asArray());
    }

    public function testIndirectRelatedEntitiesOfHigherOrder(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $termInfo = $this->getSubjectUnderTest();

        // set narrower relations
        $termInfo->addRelatedEntitiesOfLowerOrder($plant, [$tree]);

        $this->assertEquals([$plant], $termInfo->getRelatedEntitiesOfHigherOrder($tree)->asArray());
    }

    public function testIndirectRelatedEntitiesOfLowerOrder(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();

        // set broader relations
        $termInfo->addRelatedEntitiesOfHigherOrder($tree, [$plant]);

        $this->assertEquals([$tree], $termInfo->getRelatedEntitiesOfLowerOrder($plant)->asArray());
    }

    public function testAsListOfTitles(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();

        // set broader relations
        $termInfo->addRelatedEntitiesOfHigherOrder($tree, [$plant]);

        // terms in default language
        $this->assertEquals(
            ['Tree', 'Plant'],
            $termInfo->asListOfTitles()
        );

        // terms in German (de)
        $this->assertEquals(
            ['Baum', 'Pflanze'],
            $termInfo->asListOfTitles('de')
        );

        // terms in English (en)
        $this->assertEquals(
            ['Tree', 'Plant'],
            $termInfo->asListOfTitles('en')
        );
    }

    public function testAsListOfTitlesNonExistingLanguage(): void
    {
        $this->expectExceptionMessage('No names available or no name for given language.');

        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');

        $termInfo = $this->getSubjectUnderTest([$plant]);

        $termInfo->asListOfTitles('fr');
    }

    public function testSortByTitleAscendingNoLanguageGiven(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();
        $termInfo->addEntities([$tree, $plant]);

        // No language given, so it uses first matching language (en)
        $termInfo->sortByTitleAscending();

        $this->assertEquals(['Plant', 'Tree'], $termInfo->asListOfTitles());
    }

    public function testSortByTitleAscendingByLanguage(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();
        $termInfo->addEntities([$tree, $plant]);

        // sort by English titles
        $termInfo->sortByTitleAscending('en');
        $this->assertEquals(['Plant', 'Tree'], $termInfo->asListOfTitles());

        // sort by German titles
        $termInfo->sortByTitleAscending('de');
        $this->assertEquals(['Tree', 'Plant'], $termInfo->asListOfTitles());
    }

    public function testNoSorting(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();
        $termInfo->addEntities([$tree, $plant]);

        $this->assertEquals(['Tree', 'Plant'], $termInfo->asListOfTitles());
    }
}
