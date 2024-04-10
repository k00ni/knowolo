<?php

require __DIR__.'/../../scripts/vendor/autoload.php';

$startTime = microtime(true);

$memoryUsage = memory_get_usage();
$module = require __DIR__.'/STWThesaurusForEconomics.php';
echo PHP_EOL;
$usedMemory = ((memory_get_usage()-$memoryUsage)/1024/1024);
echo 'Memory usage of knowledge module: ~'.number_format($usedMemory, 2).' MB';
echo PHP_EOL;
echo 'Knowledge module contains '.count($module->getTerms()).' terms';
echo PHP_EOL;

echo PHP_EOL.'Fischerei (broader terms):';
foreach ($module->getBroaderTerms('Fischerei', 'de') as $term) {
    echo PHP_EOL.' - '.$term->getName('de');
}

echo PHP_EOL;
echo PHP_EOL.'Agricultural policy (narrower terms):';
foreach ($module->getNarrowerTerms('Agricultural policy', 'en') as $term) {
    echo PHP_EOL.' - '.$term->getName('en');
}
echo PHP_EOL;
echo PHP_EOL.'or as a list of strings: '.implode(', ', $module->getNarrowerTerms('Agricultural policy', 'en')->asListOfNames());

echo PHP_EOL;
echo PHP_EOL;
echo 'Finished script after: '. microtime(true) - $startTime.' seconds';

echo PHP_EOL;
