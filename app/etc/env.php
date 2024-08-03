<?php
return [
    'remote_storage' => [
        'driver' => 'file'
    ],
    'backend' => [
        'frontName' => 'admin_m3qvhp1'
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => '1hIwr3EziRfyeYsVHPUNzv3GQljHIamL'
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => '5c5_'
            ],
            'page_cache' => [
                'id_prefix' => '5c5_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'config' => [
        'async' => 0
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => 'base64+Y+64sAdrYp7u2nF+mIjB/5KN7jYN/NmspJ6uXM99ao='
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'appliancelondon_db',
                'username' => 'appliancelondon_user',
                'password' => 'u]ZfnSU;Fft7',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'lock' => [
        'provider' => 'db'
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 0,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 0,
        'reflection' => 0,
        'db_ddl' => 0,
        'compiled_config' => 1,
        'eav' => 0,
        'customer_notification' => 0,
        'config_integration' => 0,
        'config_integration_api' => 0,
        'graphql_query_resolver_result' => 0,
        'full_page' => 0,
        'config_webservice' => 0,
        'translate' => 0
    ],
    'downloadable_domains' => [
        'appliancecentrelondon.co.uk'
    ],
    'install' => [
        'date' => 'Sun, 28 Jul 2024 08:49:31 +0000'
    ]
];
