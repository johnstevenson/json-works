<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(E_ALL);
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($autoloader)) {
    echo "Composer autoloader not found: $autoloader" . PHP_EOL;
    echo "Please run 'composer install' and try again." . PHP_EOL;
    exit(1);
}

$loader = require $autoloader;
//$loader->addPsr4('JsonWorks\\Tests\\', __DIR__);
