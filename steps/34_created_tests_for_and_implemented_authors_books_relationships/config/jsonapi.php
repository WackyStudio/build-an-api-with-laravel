<?php
return [
    'resources' => [
        'authors' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at',
            ],
            'allowedIncludes' => [
                'books'
            ],
            'validationRules'=> [
                'create' => [
                    'data.attributes.name' => 'required|string',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string',
                ]
            ],
            'relationships' => [
                [
                    'type' => 'books',
                    'method' => 'books',
                ]
            ]
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
            'validationRules'=> [
                'create' => [
                    'data.attributes.title' => 'required|string',
                    'data.attributes.description' => 'required|string',
                    'data.attributes.publication_year' => 'required|string',
                ],
                'update' => [
                    'data.attributes.title' => 'sometimes|required|string',
                    'data.attributes.description' => 'sometimes|required|string',
                    'data.attributes.publication_year' => 'sometimes|required|string',
                ]
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