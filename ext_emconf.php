<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Enhanced Fluid based backend element previews',
    'description' => 'Adds full Fluid Templates/Layouts/Partials to backend element previews to enable consistent use of partials.',
    'category' => 'fe',
    'author' => 'David Steeb',
    'author_email' => 'typo3@b13.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 1,
    'author_company' => 'b13 GmbH, Stuttgart',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.99.99',
        ]
    ]
];
