<?php

require __DIR__.'/../../scripts/vendor/autoload.php';

$startTime = microtime(true);

// save memory usage of imported knowledge module class instance
$memoryUsage = memory_get_usage();
$module = require __DIR__.'/STWThesaurusForEconomics.php';
echo PHP_EOL;
$usedMemory = ((memory_get_usage()-$memoryUsage)/1024/1024);

// output basic information about the instance
echo 'Memory usage of knowledge module: ~'.number_format($usedMemory, 2).' MB';
echo PHP_EOL;
echo 'Knowledge module contains '.count($module->getTerms()).' terms';
echo PHP_EOL;

// example for getBroaderTerms
echo PHP_EOL.'Fischerei (broader terms):'; // Fischerei is the German term for fishery
foreach ($module->getBroaderTerms('Fischerei', 'de') as $term) {
    echo PHP_EOL.' - '.$term->getName('de');
}

// example for getNarrowerTerms
echo PHP_EOL;
echo PHP_EOL.'Agricultural policy (narrower terms):';
foreach ($module->getNarrowerTerms('Agricultural policy', 'en') as $term) {
    echo PHP_EOL.' - '.$term->getName('en');
}
echo PHP_EOL;
echo PHP_EOL.'or as a list of strings: '.implode(', ', $module->getNarrowerTerms('Agricultural policy', 'en')->asListOfNames());

// show amount of seconds this script required to run
echo PHP_EOL;
echo PHP_EOL;
echo 'Finished script after: '. microtime(true) - $startTime.' seconds';

echo PHP_EOL;
