#!/usr/bin/env php

<?php

// autoload the vendor directory
require __DIR__ . '/vendor/autoload.php';

// instantiate the console application
use Symfony\Component\Console\Application;
use emPHPasis\Commands;

$application = new Application();

// register commands
$application->add(new Commands\Initialize());

try {
    $application->run();
} catch (Throwable $e) {
    echo 'Something went wrong: ' . $e->getMessage();
}
