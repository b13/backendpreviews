<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Enhanced Fluid based backend element previews',
    'description' => 'Adds full Fluid Templates/Layouts/Partials to backend element previews to enable consistent use of partials.',
    'category' => 'fe',
    'author' => 'David Steeb',
    'author_email' => 'typo3@b13.com',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author_company' => 'b13 GmbH, Stuttgart',
    'version' => '1.4.3',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-13.99.99',
        ],
    ],
];
