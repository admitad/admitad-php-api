<?php

$config = new PhpCsFixer\Config('Admitad CS Rules');
$config
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile('/tmp/.php_cs.cache')
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,

        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'linebreak_after_opening_tag' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'normalize_index_brace' => true,
        'trailing_comma_in_multiline' => true,
        'no_empty_comment' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'return',
                'throw',
                'try',
                'foreach',
                'if',
                'switch',
                'while',
            ],
        ],
        'cast_spaces' => ['space' => 'single'],
        'class_definition' => ['multi_line_extends_each_single_line' => true],
        'concat_space' => ['spacing' => 'one'],
        'no_null_property_initialization' => true,
        'object_operator_without_whitespace' => true,
        'include' => true,
        'class_attributes_separation' => true,
        'no_unused_imports' => true,

        /** Format phpdoc **/
        'no_empty_phpdoc' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['author']],
        'phpdoc_return_self_reference' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_no_empty_return' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_no_alias_tag' => true,
        'whitespace_after_comma_in_array' => false,
        'phpdoc_no_useless_inheritdoc' => true,

        /** Change code **/
        'no_useless_else' => true,
        'no_superfluous_elseif' => true,
        'native_function_casing' => true,
        'modernize_types_casting' => true,
        'no_mixed_echo_print' => ['use' => 'echo'],
        'single_quote' => true,
        'no_alias_functions' => true,
        'no_php4_constructor' => true,
        'dir_constant' => true,
        'function_to_constant' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude([
                'vendor',
            ])
            ->in('src')
    )
;

return $config;
