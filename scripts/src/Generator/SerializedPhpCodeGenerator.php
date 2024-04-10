<?php

declare(strict_types=1);

namespace Knowolo\Generator;

use EasyRdf\Format;
use Knowolo\Config;
use Knowolo\DefaultImplementation\KnowledgeEntityList;
use Knowolo\KnowledgeEntityListInterface;

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

        // class information
        $classInformation = $config->getIncludeClassInformation()
            ? $this->buildClassInformation($graph, $config)
            : new KnowledgeEntityList();

        return $this->generateSerializedPhpCode($termsInformation, $classInformation, $config);
    }

    /**
     * @return non-empty-string
     */
    private function generateSerializedPhpCode(
        KnowledgeEntityListInterface $termsInformation,
        KnowledgeEntityListInterface $classInformation,
        Config $config
    ): string {
        $moduleClassPath = $config->getPhpModuleClasspath();
        $knowledgeModule = new $moduleClassPath($termsInformation, $classInformation);

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
