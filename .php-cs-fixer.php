<?php

require __DIR__.'/vendor/autoload.php';

return (new \MattAllan\LaravelCodeStyle\Config())
    ->setFinder(
        \PhpCsFixer\Finder::create()
            ->exclude(['bootstrap', 'docker', 'public', 'resources', 'storage'])
            ->notPath('tsugi-config.php')
            ->notPath('server.php')
            ->ignoreVCS(true)
            ->ignoreVCSIgnored(true)
            ->in(__DIR__)
    )
    ->setRules([
        '@Symfony' => true,
        '@Laravel' => true,

        /* Packback-specific style preferences */
        'not_operator_with_successor_space' => false,
        'concat_space' => ['spacing' => 'one'],
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_null_property_initialization' => true,
        'ordered_class_elements' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_order' => true,
        'phpdoc_order_by_value' => true,
        'phpdoc_types_order' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'return_assignment' => true,
        'single_line_throw' => false,
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ]);
