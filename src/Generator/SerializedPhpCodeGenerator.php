<?php

declare(strict_types=1);

namespace Knowolo\Generator;

use Knowolo\Config;
use Knowolo\DefaultImplementation\KnowledgeEntityList;
use Knowolo\KnowledgeModuleInterface;

/**
 * Generates serialized PHP code for a given set of data.
 */
class SerializedPhpCodeGenerator extends AbstractGenerator
{
    /**
     * @throws \InvalidArgumentException
     * @throws \Knowolo\Exception if RDF file does not exist
     * @throws \Knowolo\Exception if RDF file has unknown format
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \quickRdfIo\RdfIoException
     */
    public function generateFileData(string $urlOrLocalPathToRdfFile, Config $config): string
    {
        $iterator = $this->getIteratorForRdfFile($urlOrLocalPathToRdfFile);

        $graph = $this->buildGraph($iterator);

        // terms information
        $termsInformation = $config->getIncludeTermInformation()
            ? $this->buildTermInformation($graph, $config)
            : new KnowledgeEntityList();
        $termsInformation->sortByTitleAscending($config->getSortAllEntitiesByTitleAscUsingLanguage());

        // class information
        $classInformation = $config->getIncludeClassInformation()
            ? $this->buildClassInformation($graph, $config)
            : new KnowledgeEntityList();
        $classInformation->sortByTitleAscending($config->getSortAllEntitiesByTitleAscUsingLanguage());

        // init and fill module
        $moduleClassPath = $config->getPhpModuleClasspath();

        /** @var \Knowolo\KnowledgeModuleInterface */
        $knowledgeModule = new $moduleClassPath($config, $termsInformation, $classInformation);

        return $this->generateSerializedPhpCode($knowledgeModule);
    }

    /**
     * @return non-empty-string
     */
    private function generateSerializedPhpCode(KnowledgeModuleInterface $knowledgeModule): string
    {
        /** @var non-empty-string */
        $str = '<?php';
        $str .= PHP_EOL;
        $str .= PHP_EOL.'declare(strict_types=1);';
        $str .= PHP_EOL;
        $str .= '$serializedModule = <<<END';
        $str .= PHP_EOL;
        $str .= serialize($knowledgeModule);
        $str .= PHP_EOL;
        $str .= 'END;';
        $str .= PHP_EOL;
        $str .= 'return unserialize($serializedModule);';

        return $str;
    }
}
