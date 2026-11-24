<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())->in(__DIR__ . '/tests');

$rules = [
    '@auto' => true,
    '@auto:risky' => true,
    '@PhpCsFixer:risky' => true,
    'php_unit_test_case_static_method_calls' => [
        'call_type' => 'this',
    ],
];

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.test.cache')
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
