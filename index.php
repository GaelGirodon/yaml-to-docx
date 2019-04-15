<?php

use Symfony\Component\Console\Application;
use YamlToDocx\YamlToDocxCommand;

/* Autoloading */
require __DIR__ . '/vendor/autoload.php';

/* Initialize the CLI */
$application = new Application('yamltodocx', '0.1.0-dev');
$command = new YamlToDocxCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

/* Run */
try {
    $application->run();
} catch (\Exception $e) {
    echo "An error occurred running yamltodocx: " . $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}
