<?php

/*
 * Build a .phar file.
 *
 * Note: phar.readonly must be set to 0 in php.ini
 */

/* Parse CLI */
if (!defined('STDIN') || $argc != 4) {
    echo "Usage: phar.php src/ entrypoint.php dist/app.phar";
    exit(1);
}

/* Arguments */
$src = $argv[1];
$defaultStub = $argv[2];
$pharFile = $argv[3];

/* Create phar */
$phar = new Phar($pharFile);
$phar->buildFromDirectory($src);
$phar->setDefaultStub($defaultStub, "/$defaultStub");
//$p->compress(Phar::GZ);

echo "$pharFile successfully created", PHP_EOL;
