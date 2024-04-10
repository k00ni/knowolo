<?php

namespace Tests;

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

        $this->assertEquals([], $termInfo->getRelatedEntitiesOfHigherOrder($tree)->asArray());
    }

    public function testIndirectRelatedEntitiesOfLowerOrder(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();

        // set broader relations
        $termInfo->addRelatedEntitiesOfHigherOrder($tree, [$plant]);

        $this->assertEquals([], $termInfo->getRelatedEntitiesOfLowerOrder($plant)->asArray());
    }

    public function testAsListOfNames(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');

        $termInfo = $this->getSubjectUnderTest();

        // set broader relations
        $termInfo->addRelatedEntitiesOfHigherOrder($tree, [$plant]);

        // terms in default language
        $this->assertEquals(
            ['Tree', 'Plant'],
            $termInfo->asListOfNames()
        );

        // terms in German (de)
        $this->assertEquals(
            ['Baum', 'Pflanze'],
            $termInfo->asListOfNames('de')
        );

        // terms in English (en)
        $this->assertEquals(
            ['Tree', 'Plant'],
            $termInfo->asListOfNames('en')
        );
    }

    public function testAsListOfNamesNonExistingLanguage(): void
    {
        $this->expectExceptionMessage('No names available or no name for given language.');

        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');

        $termInfo = $this->getSubjectUnderTest([$plant]);

        $termInfo->asListOfNames('fr');
    }
}
