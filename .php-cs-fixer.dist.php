<?php

$finder = PhpCsFixer\Finder::create()
    ->in(
        [
            __DIR__.'/src',
            __DIR__.'/tests',
        ]
    )
;
$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        '@PER' => true,
        'ternary_to_null_coalescing' => true,
        'phpdoc_to_comment' => false,
        'single_line_throw' => false,
        'void_return' => true,
        'php_unit_test_class_requires_covers' => false,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'default',
                'for',
                'foreach',
                'if',
                'return',
                'switch',
                'throw',
                'try',
                'while',
            ],
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls',
        ],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'.php_cs.cache')
;
