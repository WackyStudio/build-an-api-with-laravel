<?php


namespace Tests\Feature;


use App\Comment;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_an_comment_as_a_resource_object()
    {
        $comment = factory(Comment::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/comments/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comment->message,
                        'created_at' => $comment->created_at->toJSON(),
                        'updated_at' => $comment->updated_at->toJSON(),
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_returns_all_comments_as_a_collection_of_resource_objects()
    {
        $comments = factory(Comment::class, 3)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/comments', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[0]->message,
                        'created_at' => $comments[0]->created_at->toJSON(),
                        'updated_at' => $comments[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[1]->message,
                        'created_at' => $comments[1]->created_at->toJSON(),
                        'updated_at' => $comments[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[2]->message,
                        'created_at' => $comments[2]->created_at->toJSON(),
                        'updated_at' => $comments[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);

    }

    /**
     * @test
     */
    public function it_can_create_a_comment_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => 'comments',
                    "attributes" => [
                        'message' => 'Hello world',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ])->assertHeader('Location', url('/api/v1/comments/1'));

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'message' => 'Hello world',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_is_given_when_creating_a_comment()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => '',
                'attributes' => [
                   'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.type field is required.',
                        'source'  => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('comments', [
            'id' => 1,
            'message' => 'Hello world',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_has_the_value_of_comments_when_creating_an_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'booo',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The selected data.type is invalid.',
                        'source'  => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_creating_a_comment()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes field is required.',
                        'source'  => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_creating_a_comment()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => 'this is not an object'
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes must be an array.',
                        'source'  => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_validates_that_a_message_attribute_is_given_when_creating_a_comment()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'something' => 'test',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes.message field is required.',
                        'source'  => [
                            'pointer' => '/data/attributes/message',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_validates_that_a_message_attribute_is_a_string_when_creating_an_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/comments', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'message' => 42,
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes.message must be a string.',
                        'source'  => [
                            'pointer' => '/data/attributes/message',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_can_update_an_book_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => 'Hello world',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'message' => 'Hello world',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_an_id_member_is_given_when_updating_a_comment()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.id field is required.',
                        'source'  => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);

    }

    /**
     * @test
     */
    public function it_validates_that_an_id_member_is_a_string_when_updating_a_comment()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => 1,
                'type' => 'comments',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.id must be a string.',
                        'source'  => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);

    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_is_given_when_updating_a_comment()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => '',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.type field is required.',
                        'source'  => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('comments', [
            'id' => 1,
            'message' => 'Hello world',
        ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_has_the_value_of_comments_when_updating_an_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => 'booo',
                'attributes' => [
                    'message' => 'Hello world',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The selected data.type is invalid.',
                        'source'  => [
                            'pointer' => '/data/type',
                        ]
                    ]
                ]
            ]);

    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_updating_a_comment()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => 'comments',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes field is required.',
                        'source'  => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_updating_a_comment()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => 'comments',
                'attributes' => 'this is not an object'
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes must be an array.',
                        'source'  => [
                            'pointer' => '/data/attributes',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_validates_that_a_message_attribute_is_a_string_when_updating_an_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->patchJson('/api/v1/comments/1', [
            'data' => [
                'id' => '1',
                'type' => 'comments',
                'attributes' => [
                    'message' => 42,
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title'   => 'Validation Error',
                        'details' => 'The data.attributes.message must be a string.',
                        'source'  => [
                            'pointer' => '/data/attributes/message',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     *
     */
    public function it_can_delete_an_book_through_a_delete_request()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $comment = factory(Comment::class)->create();

        $this->delete('/api/v1/comments/1',[], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => 1,
            'message' => $comment->message
        ]);
    }

    /**
     * @test
     */
    public function it_can_sort_comments_by_created_at_through_a_sort_query_parameter()
    {
        $comments = factory(Comment::class, 3)->create()->each(function($item, $index){
            $item->created_at = now()->addSeconds($index);
            $item->save();
        });
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/comments?sort=created_at', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[0]->message,
                        'created_at' => $comments[0]->created_at->toJSON(),
                        'updated_at' => $comments[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[1]->message,
                        'created_at' => $comments[1]->created_at->toJSON(),
                        'updated_at' => $comments[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[2]->message,
                        'created_at' => $comments[2]->created_at->toJSON(),
                        'updated_at' => $comments[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);

    }

    /**
     * @test
     */
    public function it_can_sort_comments_by_created_at_in_descending_order_through_a_sort_query_parameter()
    {
        $comments = factory(Comment::class, 3)->create()->each(function($item, $index){
            $item->created_at = now()->addSeconds($index);
            $item->save();
        });
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/comments?sort=-created_at', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '3',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[2]->message,
                        'created_at' => $comments[2]->created_at->toJSON(),
                        'updated_at' => $comments[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[1]->message,
                        'created_at' => $comments[1]->created_at->toJSON(),
                        'updated_at' => $comments[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[0]->message,
                        'created_at' => $comments[0]->created_at->toJSON(),
                        'updated_at' => $comments[0]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);

    }

    /**
     * @test
     */
    public function it_can_paginate_comments_through_a_page_query_parameter()
    {
        $comments = factory(Comment::class, 10)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/comments?page[size]=5&page[number]=1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[0]->message,
                        'created_at' => $comments[0]->created_at->toJSON(),
                        'updated_at' => $comments[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[1]->message,
                        'created_at' => $comments[1]->created_at->toJSON(),
                        'updated_at' => $comments[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[2]->message,
                        'created_at' => $comments[2]->created_at->toJSON(),
                        'updated_at' => $comments[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '4',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[3]->message,
                        'created_at' => $comments[3]->created_at->toJSON(),
                        'updated_at' => $comments[3]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '5',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[4]->message,
                        'created_at' => $comments[4]->created_at->toJSON(),
                        'updated_at' => $comments[4]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('comments.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('comments.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('comments.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);

    }

    /**
     * @test
     */
    public function it_can_paginate_comments_through_a_page_query_parameter_and_show_different_pages()
    {
        $comments = factory(Comment::class, 10)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/comments?page[size]=5&page[number]=2', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '6',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[5]->message,
                        'created_at' => $comments[5]->created_at->toJSON(),
                        'updated_at' => $comments[5]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '7',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[6]->message,
                        'created_at' => $comments[6]->created_at->toJSON(),
                        'updated_at' => $comments[6]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '8',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[7]->message,
                        'created_at' => $comments[7]->created_at->toJSON(),
                        'updated_at' => $comments[7]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '9',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[8]->message,
                        'created_at' => $comments[8]->created_at->toJSON(),
                        'updated_at' => $comments[8]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '10',
                    "type" => "comments",
                    "attributes" => [
                        'message' => $comments[9]->message,
                        'created_at' => $comments[9]->created_at->toJSON(),
                        'updated_at' => $comments[9]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('comments.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('comments.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => route('comments.index', ['page[size]' => 5, 'page[number]' => 1]),
                'next' => null,
            ]
        ]);

    }

}
