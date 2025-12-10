<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->notPath('src/Kernel.php')
    ->notPath('tests/object-manager.php')
    ->notPath('tests/bootstrap.php')
    ->notPath('public/index.php')
    ->notPath('config/preload.php')
    ->notPath('config/bundles.php')
    ->notPath('importmap.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'single_import_per_statement' => true,
        'blank_line_between_import_groups' => true,
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => false,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays'],
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'unary_operator_spaces' => true,
        'yoda_style' => true,
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'return_type_declaration' => ['space_before' => 'none'],
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'single_line_throw' => false,
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'visibility_required' => [
            'elements' => ['property', 'method', 'const'],
        ],
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
        ],
        'declare_strict_types' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => false,
        ],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
