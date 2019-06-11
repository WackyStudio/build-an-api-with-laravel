<?php
return [
    'resources' => [
        'authors' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at',
            ],
        ],
        'books' => [
            'allowedSorts'=> [
                'title',
                'publication_year',
                'created_at',
                'updated_at',
            ],
            'allowedIncludes' => [
                'authors'
            ],
            'relationships' => [
                [
                    'type' => 'authors',
                    'method' => 'authors',
                ]
            ]
        ]
    ]
];