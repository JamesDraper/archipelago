<?php

define('ARCHIPELAGO_TESTS_MIN_PHP_VERSION', '8.5.1');

if (version_compare(PHP_VERSION, ARCHIPELAGO_TESTS_MIN_PHP_VERSION, '<')) {
    echo sprintf(
        'The test suite requires PHP %s or later, but PHP %s is running',
        ARCHIPELAGO_TESTS_MIN_PHP_VERSION,
        PHP_VERSION
    );

    echo "\n";

    exit(1);
}

require dirname(__FILE__) . '/../vendor/autoload.php';
