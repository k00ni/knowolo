<?php

namespace Tests\DefaultImplementation;

use EasyRdf\Graph;
use Knowolo\Config;
use quickRdf\DataFactory;
use Test\TestCase;
use Test\TestGenerator;

class AbstractGeneratorTest extends TestCase
{
    private function getTestGraph(): Graph
    {
        /*
         * generates a Graph instance with the following content:
         *
         *      http://tree
         *          a skos:Concept ;
         *          skos:prefLabel "TREE" ;
         *          skos:broader http://plant .
         *
         *      http://plant
         *          a skos:Concept ;
         *          skos:prefLabel "plant" .
         */
        $graph = new Graph();
        $graph->addResource('http://tree', 'rdf:type', 'skos:Concept');
        $graph->resource('http://tree')->addLiteral('skos:prefLabel', 'TREE');
        $graph->resource('http://tree')->addResource('skos:broader', 'http://plant');

        $graph->addResource('http://plant', 'rdf:type', 'skos:Concept');
        $graph->resource('http://plant')->addLiteral('skos:prefLabel', 'plant');

        return $graph;
    }

    public function testAlignClassTitlesTrue(): void
    {
        $generator = new TestGenerator(new DataFactory());

        $graph = $this->getTestGraph();

        $config = new Config(json_encode([
            'align-class-titles' => true,
        ]));

        list($termInfo, ) = $generator->generateTermsAndClassInformation($graph, $config);

        $this->assertEquals(['Tree', 'Plant'], $termInfo->asListOfTitles());
    }

    public function testAlignClassTitlesFalse(): void
    {
        $generator = new TestGenerator(new DataFactory());

        $graph = $this->getTestGraph();

        $config = new Config(json_encode([
            'align-class-titles' => false,
        ]));

        list($termInfo, ) = $generator->generateTermsAndClassInformation($graph, $config);

        $this->assertEquals(['TREE', 'plant'], $termInfo->asListOfTitles());
    }
}
