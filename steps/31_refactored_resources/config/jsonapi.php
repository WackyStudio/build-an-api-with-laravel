<?php
return [
    'resources' => [
        'authors' => [],
        'books' => [
            'relationships' => [
                [
                    'type' => 'authors',
                    'method' => 'authors',
                ]
            ]
        ]
    ]
];