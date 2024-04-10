<?php

require __DIR__.'/../../scripts/vendor/autoload.php';

$startTime = microtime(true);

// save memory usage of imported knowledge module class instance
$memoryUsage = memory_get_usage();
$module = require __DIR__.'/ICF.php';
echo PHP_EOL;
$usedMemory = ((memory_get_usage()-$memoryUsage)/1024/1024);

// output basic information about the instance
echo 'Memory usage of knowledge module: ~'.number_format($usedMemory, 2).' MB';
echo PHP_EOL;
echo 'Knowledge module contains '.count($module->getClasses()).' classes';
echo PHP_EOL;
echo PHP_EOL;

// output some classes
$i = 0;
echo 'List of some classes:';
foreach ($module->getClasses() as $class) {
    echo PHP_EOL.'- '.$class->getName();
    if ($i++ > 10) {
        break;
    }
}
echo PHP_EOL;

// show amount of seconds this script required to run
echo PHP_EOL;
echo PHP_EOL;
echo 'Finished script after: '. microtime(true) - $startTime.' seconds';

echo PHP_EOL;
