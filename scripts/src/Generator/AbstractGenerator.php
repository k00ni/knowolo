<?php

declare(strict_types=1);

namespace Knowolo\Generator;

use EasyRdf\Format;
use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\Resource;
use Knowolo\Config;
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
    protected array $defaultLabelProps = [
        'dc:title',
        'dc11:title',
        'rdfs:label',
        'foaf:name',
        'skos:prefLabel',
        'skos:altLabel'
    ];

    public function __construct(DataFactoryInterface $dataFactory)
    {
        $this->dataFactory = $dataFactory;
    }

    /**
     * @param non-empty-string $urlOrLocalPathToRdfFile
     *
     * @return non-empty-string
     */
    abstract public function generateFileData(string $urlOrLocalPathToRdfFile, Config $config): string;

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
     * @param non-empty-string $uri
     *
     * @return non-empty-string
     */
    private function buildClassId(string $uri, Config $config): string
    {
        if ($config->getCompressClassIds()) {
            $shortId = hash('sha512', $uri);
            $shortId = 'class/'.substr($shortId, 0, 8);
            return $shortId;
        } else {
            return $uri;
        }
    }

    /**
     * @param non-empty-string $uri
     *
     * @return non-empty-string
     */
    private function buildTermId(string $uri, Config $config): string
    {
        if ($config->getCompressTermIds()) {
            $shortId = hash('sha512', $uri);
            $shortId = 'term/'.substr($shortId, 0, 8);
            return $shortId;
        } else {
            return $uri;
        }
    }

    /**
     * @return array<string,non-empty-string>
     */
    private function buildLanguageToNamesList(Resource $resource, Config $config): array
    {
        $labelProps = 0 < count($config->getCustomLabelPropertiesForClasses())
            ? $config->getCustomLabelPropertiesForClasses()
            : $this->defaultLabelProps;

        $languageToNamesList = [];
        foreach ($config->getPreferedLanguages() as $lang) {
            $value = $resource->label($lang, $labelProps)?->getValue() ?? null;

            if (is_scalar($value) && false === isEmpty($value)) {
                /** @var non-empty-string */
                $value = (string) $value;
                $languageToNamesList[$lang] = $value;
            }
        }

        if (0 == count($languageToNamesList)) {
            $value = $resource->label(null, $labelProps)?->getValue() ?? null;
            if (is_scalar($value) && false === isEmpty($value)) {
                /** @var non-empty-string */
                $value = (string) $value;

                // align class title, if set to true in config
                if ($config->getAlignClassTitles()) {
                    $value = mb_strtolower($value);
                    $value = ucfirst($value);
                }

                $languageToNamesList[''] = $value;
            }
        }

        return $languageToNamesList;
    }

    /**
     * Builds class information.
     *
     * @throws \Knowolo\Exception
     */
    protected function buildClassInformation(Graph $graph, Config $config): KnowledgeEntityListInterface
    {
        $knowledgeEntityList = new KnowledgeEntityList();

        foreach ($graph->allOfType('owl:Class') as $resource) {
            $langToNames = $this->buildLanguageToNamesList($resource, $config);
            if (0 == count($langToNames)) {
                continue;
            }

            $entity = new KnowledgeEntity($langToNames, $this->buildClassId($resource->getUri(), $config));
            $knowledgeEntityList->add($entity);

            // super class
            foreach ($resource->allResources('rdfs:subClassOf') as $relatedEntity) {
                $langToNames = $this->buildLanguageToNamesList($relatedEntity, $config);
                if (0 == count($langToNames)) {
                    continue;
                }

                $relatedEntity = new KnowledgeEntity($langToNames, $this->buildClassId($relatedEntity->getUri(), $config));
                $knowledgeEntityList->addRelatedEntitiesOfHigherOrder($entity, [$relatedEntity]);
            }
        }

        return $knowledgeEntityList;
    }

    /**
     * Builds terms information.
     *
     * @throws \Knowolo\Exception
     */
    protected function buildTermInformation(Graph $graph, Config $config): KnowledgeEntityListInterface
    {
        $knowledgeEntityList = new KnowledgeEntityList();

        foreach ($graph->allOfType('skos:Concept') as $resource) {
            $langToNames = $this->buildLanguageToNamesList($resource, $config);
            if (0 == count($langToNames)) {
                continue;
            }

            $entity = new KnowledgeEntity($langToNames, $this->buildTermId($resource->getUri(), $config));
            $knowledgeEntityList->add($entity);

            // broader terms
            foreach ($resource->allResources('skos:broader') as $relatedEntity) {
                $langToNames = $this->buildLanguageToNamesList($relatedEntity, $config);
                if (0 == count($langToNames)) {
                    continue;
                }
                $relatedEntity = new KnowledgeEntity($langToNames, $this->buildTermId($relatedEntity->getUri(), $config));
                $knowledgeEntityList->addRelatedEntitiesOfHigherOrder($entity, [$relatedEntity]);
            }

            // narrower terms
            foreach ($resource->allResources('skos:narrower') as $relatedEntity) {
                $langToNames = $this->buildLanguageToNamesList($relatedEntity, $config);
                if (0 == count($langToNames)) {
                    continue;
                }

                $relatedEntity = new KnowledgeEntity($langToNames, $this->buildTermId($relatedEntity->getUri(), $config));
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
