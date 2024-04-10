<?php

declare(strict_types=1);

namespace Knowolo\Generator;

use EasyRdf\Format;
use EasyRdf\Graph;
use EasyRdf\Literal;
use Knowolo\Exception;
use Knowolo\DefaultImplementation\KnowledgeEntity;
use Knowolo\DefaultImplementation\KnowledgeEntityList;
use Knowolo\KnowledgeEntityListInterface;
use quickRdfIo\Util;
use rdfInterface\DataFactoryInterface;
use rdfInterface\LiteralInterface;
use rdfInterface\NamedNodeInterface;
use rdfInterface\QuadIteratorInterface;

use function Knowolo\isEmpty;
use function Knowolo\sendCachedRequest;

abstract class AbstractGenerator
{
    protected DataFactoryInterface $dataFactory;

    /**
     * @var array<non-empty-string>
     */
    protected array $labelProps = ['dc:title', 'dc11:title', 'rdfs:label', 'foaf:name', 'skos:prefLabel'];

    public function __construct(DataFactoryInterface $dataFactory)
    {
        $this->dataFactory = $dataFactory;
    }

    /**
     * @param non-empty-string $urlOrLocalPathToRdfFile
     *
     * @return non-empty-string
     */
    abstract public function generateFileData(string $urlOrLocalPathToRdfFile): string;

    /**
     * Builds an EasyRdf Graph instance which is used the query the data later on.
     *
     * @throws \InvalidArgumentException
     */
    protected function buildGraph(QuadIteratorInterface $iterator): Graph
    {
        $graph = new Graph();

        foreach ($iterator as $quad) {
            // PHPStan acrobatics (mixed vs. string)
            /** @var string|null */
            $s = $quad->getSubject()->getValue();
            $s = (string) $s;

            $p = $quad->getPredicate()->getValue();

            // add triple to graph
            $res = $graph->resource($s);
            $o = null;

            if ($quad->getObject() instanceof LiteralInterface) {
                // cast value to float or string
                // PHPStan acrobatics (mixed vs. string)
                /** @var string|null */
                $o = $quad->getObject()->getValue();
                $o = new Literal(
                    (string) $o,
                    $quad->getObject()->getLang(),
                    isEmpty($quad->getObject()->getLang()) ? $quad->getObject()->getDatatype() : null
                );
            } elseif ($quad->getObject() instanceof NamedNodeInterface) {
                // PHPStan acrobatics (mixed vs. string)
                /** @var string|null */
                $o = $quad->getObject()->getValue();
                $o = $graph->resource($o);
            } else {
                // ignore blank nodes
                continue;
            }

            // add property + object to resource (= $s)
            $res->add($p, $o);
        }

        return $graph;
    }

    /**
     * Builds terms information.
     *
     * @throws \Knowolo\Exception
     */
    protected function buildTerms(Graph $graph): KnowledgeEntityListInterface
    {
        $knowledgeEntityList = new KnowledgeEntityList();

        foreach ($graph->allOfType('skos:Concept') as $resource) {
            $entity = new KnowledgeEntity(
                [
                    'de' => $resource->label('de', $this->labelProps)?->getValue() ?? null,
                    'en' => $resource->label('en', $this->labelProps)?->getValue() ?? null,
                ],
                $resource->getUri(),
            );
            $knowledgeEntityList->add($entity);

            // broader terms
            foreach ($resource->allResources('skos:broader') as $relatedEntity) {
                $relatedEntity = new KnowledgeEntity(
                    [
                        'de' => $relatedEntity->label('de', $this->labelProps)?->getValue() ?? null,
                        'en' => $relatedEntity->label('en', $this->labelProps)?->getValue() ?? null,
                    ],
                    $relatedEntity->getUri(),
                );
                $knowledgeEntityList->addRelatedEntitiesOfHigherOrder($entity, [$relatedEntity]);
            }

            // narrower terms
            foreach ($resource->allResources('skos:narrower') as $relatedEntity) {
                $relatedEntity = new KnowledgeEntity(
                    [
                        'de' => $relatedEntity->label('de', $this->labelProps)?->getValue() ?? null,
                        'en' => $relatedEntity->label('en', $this->labelProps)?->getValue() ?? null,
                    ],
                    $relatedEntity->getUri(),
                );
                $knowledgeEntityList->addRelatedEntitiesOfLowerOrder($entity, [$relatedEntity]);
            }
        }

        return $knowledgeEntityList;
    }

    /**
     * @param non-empty-string $urlOrLocalPathToRdfFile
     *
     * @throws \Knowolo\Exception if RDF file does not exist
     * @throws \Knowolo\Exception if RDF file has unknown format
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \quickRdfIo\RdfIoException
     */
    protected function getIteratorForRdfFile(string $urlOrLocalPathToRdfFile): QuadIteratorInterface
    {
        // is url?
        if (str_starts_with($urlOrLocalPathToRdfFile, 'http')) {
            $rdfContent = sendCachedRequest($urlOrLocalPathToRdfFile);
        } elseif (file_exists($urlOrLocalPathToRdfFile)) {
            // local file
            $rdfContent = (string) file_get_contents($urlOrLocalPathToRdfFile);
        } else {
            $msg = 'It seems that given path to RDF file is neither an URL nor an existing, local file: '.$urlOrLocalPathToRdfFile;
            throw new Exception($msg);
        }

        $format = Format::guessFormat($rdfContent)?->getName() ?? null;
        if (null == $format) {
            throw new Exception('File has unknown format');
        }

        // parse RDF and create iterator instance
        return Util::parse($rdfContent, $this->dataFactory, $format);
    }
}
