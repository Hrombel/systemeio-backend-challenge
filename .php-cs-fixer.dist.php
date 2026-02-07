<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
        ],
        'blank_lines_before_namespace' => [
            'min_line_breaks' => 0, 
            'max_line_breaks' => 0,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
