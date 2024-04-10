<?php

declare(strict_types=1);

namespace Knowolo\Generator;

use EasyRdf\Format;
use Knowolo\KnowledgeEntityListInterface;

/**
 * Generates readable PHP code for a given set of data. It takes an order of magnitude
 * more time to consume plain PHP code instead of a serialized PHP code. This generator
 * is only provided for debugging reasons.
 */
class PhpCodeGenerator extends AbstractGenerator
{
    /**
     * @throws \InvalidArgumentException
     * @throws \Knowolo\Exception if RDF file does not exist
     * @throws \Knowolo\Exception if RDF file has unknown format
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \quickRdfIo\RdfIoException
     */
    public function generateFileData(string $urlOrLocalPathToRdfFile): string
    {
        $iterator = $this->getIteratorForRdfFile($urlOrLocalPathToRdfFile);

        $graph = $this->buildGraph($iterator);

        $termsInformation = $this->buildTerms($graph);

        return $this->generatePhpCode($termsInformation);
    }

    /**
     * @return non-empty-string
     */
    private function generatePhpCode(KnowledgeEntityListInterface $termsInformation): string
    {
        /** @var array<non-negative-integer,non-empty-string> */
        $entityRegister = [];

        $str = '<?php';
        $str .= PHP_EOL;
        $str .= PHP_EOL.'declare(strict_types=1);';
        $str .= PHP_EOL;
        $str .= PHP_EOL.'namespace Examples;';
        $str .= PHP_EOL;
        $str .= PHP_EOL.'use \\Knowolo\\DefaultImplementation\\KnowledgeModule;';
        $str .= PHP_EOL;
        $str .= PHP_EOL.'class STWThesaurusForEconomics extends KnowledgeModule';
        $str .= PHP_EOL.'{';
        $str .= PHP_EOL.'    public function init(): void';
        $str .= PHP_EOL.'    {';

        // add terms
        foreach ($termsInformation->asArray() as $index => $entity) {
            $entityRegister[$entity->getId()] = $index;

            $str .= PHP_EOL.'        $entity'.$index.' = '.$entity->asPhpCodeNew().';';
        }
        $str .= PHP_EOL;

        // add relations to broader/narrower terms
        foreach ($termsInformation->asArray() as $index => $entity) {
            // higher order
            $list = $termsInformation->getRelatedEntitiesOfHigherOrder($entity);
            if (0 < count($list)) {
                $str .= PHP_EOL.'        $this->termInformation->addRelatedEntitiesOfHigherOrder(';
                $str .= PHP_EOL.'            $entity'.$index.',';
                $str .= PHP_EOL.'            [';
                foreach ($list as $relatedEntity) {
                    // it assumes each related entry has a valid entry
                    $str .= '$entity'.$entityRegister[$relatedEntity->getId()].',';
                }
                $str .= ']';
                $str .= PHP_EOL.'        );';
            }

            // lower order
            $list = $termsInformation->getRelatedEntitiesOfLowerOrder($entity);
            if (0 < count($list)) {
                $str .= PHP_EOL.'        $this->termInformation->addRelatedEntitiesOfLowerOrder(';
                $str .= PHP_EOL.'            $entity'.$index.',';
                $str .= PHP_EOL.'            [';
                foreach ($list as $relatedEntity) {
                    // it assumes each related entry has a valid entry
                    $str .= '$entity'.$entityRegister[$relatedEntity->getId()].',';
                }
                $str .= ']';
                $str .= PHP_EOL.'        );';
            }
        }

        // footer
        $str .= PHP_EOL.'    }';
        $str .= PHP_EOL.'}';

        return $str;
    }
}
