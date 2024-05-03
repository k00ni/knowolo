<?php

require __DIR__.'/../../vendor/autoload.php';

$startTime = microtime(true);

// save memory usage of imported knowledge module class instance
$memoryUsage = memory_get_usage();
$module = require __DIR__.'/ICF.php';

echo PHP_EOL.'General Information:';
echo PHP_EOL.'- Title: '.$module->getTitle();
echo PHP_EOL.'- Summary: '.$module->getSummary();
echo PHP_EOL.'- Homepage: '.$module->getHomepage();
echo PHP_EOL;

$usedMemory = ((memory_get_usage()-$memoryUsage)/1024/1024);

// output basic information about the instance
echo PHP_EOL.'Memory usage of knowledge module: ~'.number_format($usedMemory, 2).' MB';
echo PHP_EOL;
echo 'Knowledge module contains '.count($module->getClasses()).' classes';
echo PHP_EOL;
echo PHP_EOL;

// output some classes
$i = 0;
echo 'List of some classes:';
foreach ($module->getClasses() as $class) {
    echo PHP_EOL.'- '.$class->getTitle();
    if (0 < count($module->getSubClasses($class))) {
        foreach ($module->getSubClasses($class) as $subClass) {
            echo PHP_EOL.'  `- '.$subClass->getTitle();
        }
    }

    if ($i++ > 15) {
        break;
    }
}
echo PHP_EOL;

// show amount of seconds this script required to run
echo PHP_EOL;
echo 'Finished script after: '. microtime(true) - $startTime.' seconds';

echo PHP_EOL;
