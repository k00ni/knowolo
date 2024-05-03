<?php

require __DIR__.'/../../scripts/vendor/autoload.php';

$startTime = microtime(true);

// save memory usage of imported knowledge module class instance
$memoryUsage = memory_get_usage();
$module = require __DIR__.'/STWThesaurusForEconomics.php';
$usedMemory = ((memory_get_usage()-$memoryUsage)/1024/1024);

echo PHP_EOL.'General Information:';
echo PHP_EOL.'- Title: '.$module->getTitle();
echo PHP_EOL.'- Summary: '.$module->getSummary();
echo PHP_EOL.'- License: '.$module->getLicense();
echo PHP_EOL.'- Homepage: '.$module->getHomepage();
echo PHP_EOL.'- Authors: '.$module->getAuthors();
echo PHP_EOL;

// output basic information about the instance
echo PHP_EOL.'Memory usage of knowledge module: ~'.number_format($usedMemory, 2).' MB';
echo PHP_EOL;
echo 'Knowledge module contains '.$module->getTerms()->count().' terms and '.$module->getClasses()->count().' classes';
echo PHP_EOL;

// example for getBroaderTerms
echo PHP_EOL.'Fischerei (broader terms):'; // Fischerei is the German term for fishery
foreach ($module->getBroaderTerms('Fischerei', 'de') as $term) {
    echo PHP_EOL.' - '.$term->getTitle('de');
}

// example for getNarrowerTerms
echo PHP_EOL;
echo PHP_EOL.'Agricultural policy (narrower terms):';
foreach ($module->getNarrowerTerms('Agricultural policy', 'en') as $term) {
    echo PHP_EOL.' - '.$term->getTitle('en');
}
echo PHP_EOL;
// TODO why does list contain only German terms?!
echo PHP_EOL.'or as a list of strings: '.implode(', ', $module->getNarrowerTerms('Agricultural policy', 'en')->asListOfTitles());

// show amount of seconds this script required to run
echo PHP_EOL;
echo PHP_EOL;
echo 'Finished script after: '. microtime(true) - $startTime.' seconds';

echo PHP_EOL;
