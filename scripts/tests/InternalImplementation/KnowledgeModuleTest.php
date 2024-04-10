<?php

namespace Tests;

use Knowolo\DefaultImplementation\KnowledgeEntity;
use Knowolo\DefaultImplementation\KnowledgeEntityList;
use Knowolo\DefaultImplementation\KnowledgeModule;
use Test\TestCase;

class KnowledgeModuleTest extends TestCase
{
    public function testGetSuperClasses(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $classInformation = new KnowledgeEntityList();

        // broader
        $classInformation->addRelatedEntitiesOfHigherOrder($tree, [$plant]);
        $classInformation->addRelatedEntitiesOfHigherOrder($oak, [$tree]);

        // subject under test
        $sut = new KnowledgeModule(null, $classInformation);

        $this->assertEquals([$plant], $sut->getSuperClasses($tree)->asArray());
        $this->assertEquals([$plant], $sut->getSuperClasses('Tree')->asArray());
    }

    public function testGetSubClasses(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $classInformation = new KnowledgeEntityList();

        // broader
        $classInformation->addRelatedEntitiesOfLowerOrder($plant, [$tree]);
        $classInformation->addRelatedEntitiesOfLowerOrder($tree, [$oak]);

        // subject under test
        $sut = new KnowledgeModule(null, $classInformation);

        $this->assertEquals([$oak], $sut->getSubClasses($tree)->asArray());
        $this->assertEquals([$oak], $sut->getSubClasses('Tree')->asArray());
    }

    public function testGetBroaderTerms(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $termInfo = new KnowledgeEntityList();

        // broader
        $termInfo->addRelatedEntitiesOfHigherOrder($tree, [$plant]);
        $termInfo->addRelatedEntitiesOfHigherOrder($oak, [$tree]);

        // subject under test
        $sut = new KnowledgeModule($termInfo);

        $this->assertEquals(['Plant'], $sut->getBroaderTerms('Tree')->asListOfNames());
    }

    public function testGetNarrowerTerms(): void
    {
        $plant = new KnowledgeEntity(['en' => 'Plant', 'de' => 'Pflanze'], 'http://id/plant');
        $tree = new KnowledgeEntity(['en' => 'Tree', 'de' => 'Baum'], 'http://id/tree');
        $oak = new KnowledgeEntity(['en' => 'Oak', 'de' => 'Eiche'], 'http://id/oak');

        $termInfo = new KnowledgeEntityList();

        // broader
        $termInfo->addRelatedEntitiesOfLowerOrder($plant, [$tree]);
        $termInfo->addRelatedEntitiesOfLowerOrder($tree, [$oak]);

        // subject under test
        $sut = new KnowledgeModule($termInfo);

        $this->assertEquals(['Oak'], $sut->getNarrowerTerms('Tree')->asListOfNames());
    }
}
