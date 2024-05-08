<?php

declare(strict_types=1);

namespace Test;

use EasyRdf\Graph;
use Knowolo\Config;
use Knowolo\Exception\KnowoloException;
use Knowolo\Generator\AbstractGenerator;

/**
 * Dummy class which allows integration tests for AbstractGenerator
 */
class TestGenerator extends AbstractGenerator
{
    /**
     * @throws \Knowolo\Exception\KnowoloException
     */
    public function generateFileData(string $urlOrLocalPathToRdfFile, Config $config): string
    {
        throw new KnowoloException('Not implemented yet');
    }

    /**
     * @return array<\Knowolo\KnowledgeEntityListInterface>
     *
     * @throws \ValueError
     * @throws \Knowolo\Exception\KnowoloException
     */
    public function generateTermsAndClassInformation(Graph $graph, Config $config): array
    {
        return [
            $this->buildTermInformation($graph, $config),
            $this->buildClassInformation($graph, $config)
        ];
    }
}
