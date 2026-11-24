<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())->in(__DIR__ . '/archipelago');

$rules = [
    '@PER-CS3x0' => true,
    '@PER-CS3x0:risky' => true,
    '@PhpCsFixer:risky' => true,

    // PHP 5.0 syntax limits
    'array_syntax' => ['syntax' => 'long'],                        // [] is 5.4
    'trailing_comma_in_multiline' => ['elements' => ['arrays']],   // args 7.3, params 8.0
    'modifier_keywords' => ['elements' => ['method', 'property']], // const visibility is 7.1
    'declare_strict_types' => false,                               // 7.0
    'void_return' => false,                                        // 7.1
    'dir_constant' => false,                                       // __DIR__ is 5.3
    'combine_nested_dirname' => false,                             // dirname($p, $levels) is 7.0
    'ternary_to_elvis_operator' => false,                          // ?: is 5.3
    'pow_to_exponentiation' => false,                              // ** is 5.6
    'modern_serialization_methods' => false,                       // __serialize is 7.4
    'native_function_invocation' => false,                         // leading \ needs 5.3 namespaces
    'native_constant_invocation' => false,
    'function_to_constant' => [                                    // drop get_class -> ::class (5.5)
        'functions' => ['php_sapi_name', 'phpversion', 'pi'],
    ],

    // Behaviour
    'final_internal_class' => false, // Mockery mocks @internal classes in tests
];

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.src.cache')
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
