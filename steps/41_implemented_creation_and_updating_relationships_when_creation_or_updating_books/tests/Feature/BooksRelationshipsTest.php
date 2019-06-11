<?php


namespace Tests\Feature;


use App\Author;
use App\Book;
use App\Comment;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_a_relationship_to_authors_adhering_to_json_api_spec()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);


        $this->getJson('/api/v1/books/1?include=authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'books',
                'relationships' => [
                    'authors' => [
                        'links' => [
                            'self' => route(
                                'books.relationships.authors',
                                ['id' => $book->id]
                            ),
                            'related' => route(
                                'books.authors',
                                ['id' => $book->id]
                            ),
                        ],
                        'data' => [
                            [
                                'id' => $authors->get(0)->id,
                                'type' => 'authors'
                            ],
                            [
                                'id' => $authors->get(1)->id,
                                'type' => 'authors'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_returns_a_relationship_to_comments_adhering_to_json_api_spec()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 3)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);


        $this->getJson('/api/v1/books/1?include=comments', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'books',
                    'relationships' => [
                        'comments' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.comments',
                                    ['id' => $book->id]
                                ),
                                'related' => route(
                                    'books.comments',
                                    ['id' => $book->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $comments->get(0)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => $comments->get(1)->id,
                                    'type' => 'comments'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_authors_returns_all_related_authors_as_resource_id_objects()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1/relationships/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     [
                         'id' => '1',
                         'type' => 'authors',
                     ],
                     [
                         'id' => '2',
                         'type' => 'authors',
                     ],
                     [
                         'id' => '3',
                         'type' => 'authors',
                     ],
                 ]
             ]);
    }

    /**
     * @test
     * @watch
     */
    public function a_relationship_link_to_comments_returns_all_related_comments_as_resource_id_objects()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 3)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1/relationships/comments', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'comments',
                    ],
                    [
                        'id' => '2',
                        'type' => 'comments',
                    ],
                    [
                        'id' => '3',
                        'type' => 'comments',
                    ],
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_authors_and_add_new_relationships()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 10)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'authors',
                ],
                [
                    'id' => '6',
                    'type' => 'authors',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('author_book', [
           'author_id' => 5,
           'book_id' => 1,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 6,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_comments_and_add_new_relationships()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 10)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/comments',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'comments',
                ],
                [
                    'id' => '6',
                    'type' => 'comments',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 5,
            'book_id' => 1,
        ])->assertDatabaseHas('comments', [
            'id' => 6,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_authors_and_remove_relationships()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => '1',
                    'type' => 'authors',
                ],
                [
                    'id' => '2',
                    'type' => 'authors',
                ],
                [
                    'id' => '5',
                    'type' => 'authors',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 1,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 2,
            'book_id' => 1,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 5,
            'book_id' => 1,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 3,
            'book_id' => 1,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 4,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_comments_and_remove_relationships()
    {
        $this->withoutExceptionHandling();
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 5)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/comments',[
            'data' => [
                [
                    'id' => '1',
                    'type' => 'comments',
                ],
                [
                    'id' => '2',
                    'type' => 'comments',
                ],
                [
                    'id' => '5',
                    'type' => 'comments',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('comments', [
            'id' => 1,
            'book_id' => 1,
        ])->assertDatabaseHas('comments', [
            'id' => 2,
            'book_id' => 1,
        ])->assertDatabaseHas('comments', [
            'id' => 5,
            'book_id' => 1,
        ])->assertDatabaseMissing('comments', [
            'id' => 3,
            'book_id' => 1,
        ])->assertDatabaseMissing('comments', [
            'id' => 4,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_remove_all_relationships_to_authors_with_an_empty_collection()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => []
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 1,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 2,
            'book_id' => 1,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 3,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_remove_all_relationships_to_comments_with_an_empty_collection()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 5)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/comments',[
            'data' => []
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => 1,
            'book_id' => 1,
        ])->assertDatabaseMissing('comments', [
            'id' => 2,
            'book_id' => 1,
        ])->assertDatabaseMissing('comments', [
            'id' => 3,
            'book_id' => 1,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_author()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'authors',
                ],
                [
                    'id' => '6',
                    'type' => 'authors',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(404)->assertJson([
            'errors' => [
                [
                    'title'   => 'Not Found Http Exception',
                    'details' => 'Resource not found',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_comment()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 5)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/comments',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'comments',
                ],
                [
                    'id' => '6',
                    'type' => 'comments',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(404)->assertJson([
            'errors' => [
                [
                    'title'   => 'Not Found Http Exception',
                    'details' => 'Resource not found',
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_id_member_is_given_when_updating_a_relationship()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'type' => 'authors',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.0.id field is required.',
                    'source' => [
                        'pointer' => '/data/0/id',
                    ]
                ]
            ]
        ]);
    }


    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_id_member_is_a_string_when_updating_a_relationship()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => 5,
                    'type' => 'authors',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.0.id must be a string.',
                    'source' => [
                        'pointer' => '/data/0/id',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_is_given_when_updating_a_relationship()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => '5',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The data.0.type field is required.',
                    'source' => [
                        'pointer' => '/data/0/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_validates_that_the_type_member_has_a_value_of_authors_when_updating_a_relationship()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/books/1/relationships/authors',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'random',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title'   => 'Validation Error',
                    'details' => 'The selected data.0.type is invalid.',
                    'source' => [
                        'pointer' => '/data/0/type',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_get_all_related_authors_as_resource_objects_from_related_link()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1/authors',[
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     [
                         "id" => '1',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[0]->name,
                             'created_at' => $authors[0]->created_at->toJSON(),
                             'updated_at' => $authors[0]->updated_at->toJSON(),
                         ]
                     ],
                     [
                         "id" => '2',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[1]->name,
                             'created_at' => $authors[1]->created_at->toJSON(),
                             'updated_at' => $authors[1]->updated_at->toJSON(),
                         ]
                     ],
                     [
                         "id" => '3',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[2]->name,
                             'created_at' => $authors[2]->created_at->toJSON(),
                             'updated_at' => $authors[2]->updated_at->toJSON(),
                         ]
                     ],
                 ]
             ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_get_all_related_comments_as_resource_objects_from_related_link()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 5)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1/comments',[
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
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
     * @watch
     */
    public function it_includes_related_resource_objects_for_authors_when_an_include_query_param_to_authors_is_given()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1?include=authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => '1',
                     'type' => 'books',
                     'relationships' => [
                         'authors' => [
                             'links' => [
                                 'self' => route(
                                     'books.relationships.authors',
                                     ['id' => $book->id]
                                 ),
                                 'related' => route(
                                     'books.authors',
                                     ['id' => $book->id]
                                 ),
                             ],
                             'data' => [
                                 [
                                     'id' => (string)$authors->get(0)->id,
                                     'type' => 'authors'
                                 ],
                                 [
                                     'id' => (string)$authors->get(1)->id,
                                     'type' => 'authors'
                                 ],
                                 [
                                     'id' => (string)$authors->get(2)->id,
                                     'type' => 'authors'
                                 ]
                             ]
                         ]
                     ]
                 ],
                 'included' => [
                     [
                         "id" => '1',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[0]->name,
                             'created_at' => $authors[0]->created_at->toJSON(),
                             'updated_at' => $authors[0]->updated_at->toJSON(),
                         ]
                     ],
                     [
                         "id" => '2',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[1]->name,
                             'created_at' => $authors[1]->created_at->toJSON(),
                             'updated_at' => $authors[1]->updated_at->toJSON(),
                         ]
                     ],
                     [
                         "id" => '3',
                         "type" => "authors",
                         "attributes" => [
                             'name' => $authors[2]->name,
                             'created_at' => $authors[2]->created_at->toJSON(),
                             'updated_at' => $authors[2]->updated_at->toJSON(),
                         ]
                     ],
                 ]
             ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_includes_related_resource_objects_for_comments_when_an_include_query_param_to_comments_is_given()
    {
        $book = factory(Book::class)->create();
        $comments = factory(Comment::class, 3)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1?include=comments', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'books',
                    'relationships' => [
                        'comments' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.comments',
                                    ['id' => $book->id]
                                ),
                                'related' => route(
                                    'books.comments',
                                    ['id' => $book->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => (string)$comments->get(0)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => (string)$comments->get(1)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => (string)$comments->get(2)->id,
                                    'type' => 'comments'
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
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
     * @watch
     */
    public function it_includes_related_resource_objects_for_authors_and_comments_when_an_include_query_param_to_both_is_given()
    {
        $book = factory(Book::class)->create();

        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $comments = factory(Comment::class, 3)->make();
        $book->comments()->saveMany($comments);

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1?include=authors,comments', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'books',
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $book->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $book->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => (string)$authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => (string)$authors->get(1)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => (string)$authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ],
                        'comments' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.comments',
                                    ['id' => $book->id]
                                ),
                                'related' => route(
                                    'books.comments',
                                    ['id' => $book->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => (string)$comments->get(0)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => (string)$comments->get(1)->id,
                                    'type' => 'comments'
                                ],
                                [
                                    'id' => (string)$comments->get(2)->id,
                                    'type' => 'comments'
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    [
                        "id" => '1',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[0]->name,
                            'created_at' => $authors[0]->created_at->toJSON(),
                            'updated_at' => $authors[0]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '2',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[1]->name,
                            'created_at' => $authors[1]->created_at->toJSON(),
                            'updated_at' => $authors[1]->updated_at->toJSON(),
                        ]
                    ],
                    [
                        "id" => '3',
                        "type" => "authors",
                        "attributes" => [
                            'name' => $authors[2]->name,
                            'created_at' => $authors[2]->created_at->toJSON(),
                            'updated_at' => $authors[2]->updated_at->toJSON(),
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
     * @watch
     */
    public function it_does_not_include_related_resource_objects_when_an_include_query_param_is_not_given()
    {
        $this->withoutExceptionHandling();
        $book = factory(Book::class)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJsonMissing([
                'included' => [],
             ]);
    }

    /**
     * @test
     */
    public function it_includes_related_resource_objects_for_a_collection_when_an_include_query_param_is_given()
    {
        $books = factory(Book::class, 3)->create();
        $authors = factory(Author::class, 3)->create();
        $books->each(function($book, $key) use($authors){
            if($key === 0){
                $book->authors()->sync($authors->pluck('id'));
            }
        });

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/books?include=authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[0]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[0]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(1)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[1]->title,
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[1]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[1]->id]
                                ),
                            ],
                        ]
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[2]->title,
                        'description' => $books[2]->description,
                        'publication_year' => $books[2]->publication_year,
                        'created_at' => $books[2]->created_at->toJSON(),
                        'updated_at' => $books[2]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[2]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[2]->id]
                                ),
                            ],
                        ]
                    ]
                ],
            ],
            'included' => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_does_not_include_related_resource_objects_for_a_collection_when_an_include_query_param_is_not_given()
    {
        $books = factory(Book::class, 3)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)
          ->assertJsonMissing([
              'included' => [],
          ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_only_includes_a_related_resource_object_once_for_a_collection()
    {
        $books = factory(Book::class, 3)->create();
        $authors = factory(Author::class, 3)->create();
        $books->each(function($book, $key) use($authors){
            $book->authors()->sync($authors->pluck('id'));
        });

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/books?include=authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[0]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[0]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(1)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[1]->title,
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[1]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[1]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(1)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[2]->title,
                        'description' => $books[2]->description,
                        'publication_year' => $books[2]->publication_year,
                        'created_at' => $books[2]->created_at->toJSON(),
                        'updated_at' => $books[2]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => $books[2]->id]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => $books[2]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(1)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'included' => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ])->assertJsonMissing([
            'included' => [
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[0]->name,
                        'created_at' => $authors[0]->created_at->toJSON(),
                        'updated_at' => $authors[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[1]->name,
                        'created_at' => $authors[1]->created_at->toJSON(),
                        'updated_at' => $authors[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $authors[2]->name,
                        'created_at' => $authors[2]->created_at->toJSON(),
                        'updated_at' => $authors[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /** @test */
    public function when_creating_a_book_it_can_also_add_relationships_right_away()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $authors = factory(Author::class, 2)->create();

        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            [
                                'id' => (string)$authors[0]->id,
                                'type' => 'authors',
                            ],
                            [
                                'id' => (string)$authors[1]->id,
                                'type' => 'authors',
                            ],
                        ]
                    ]
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
                    "type" => 'books',
                    "attributes" => [
                        'title' => 'Building an API with Laravel',
                        'description' => 'A book about API development',
                        'publication_year' => '2019',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => 1]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => 1]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(0)->id,
                                    'type' => 'authors'
                                ],
                                [
                                    'id' => $authors->get(1)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]

                ]
            ])->assertHeader('Location', url('/api/v1/books/1'));

        $this->assertDatabaseHas('books', [
            'id' => 1,
            'title' => 'Building an API with Laravel',


        ])->assertDatabaseHas('author_book', [
            'book_id' => 1,
            'author_id' => $authors[0]->id,
        ]);

    }

    /**
     * @test
     */
    public function it_validates_relationships_given_when_creating_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $authors = factory(Author::class, 2)->create();

        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            [
                                'id' => $authors[1]->id,
                                'type' => 'authors',
                            ],
                            [
                                'id' => (string)$authors[1]->id,
                                'type' => 'random',
                            ],
                        ]
                    ]
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.relationships.authors.data.0.id must be a string.',
                    'source' => [
                        'pointer' => '/data/relationships/authors/data/0/id',
                    ]
                ],
                [
                    'title' => 'Validation Error',
                    'details' => 'The selected data.relationships.authors.data.1.type is invalid.',
                    'source' => [
                        'pointer' => '/data/relationships/authors/data/1/type',
                    ]
                ],


            ]
        ]);

    }

    /**
     * @test
     */
    public function when_updating_a_book_it_can_also_update_relationships()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $book = factory(Book::class)->create();

        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));


        $this->patchJson('/api/v1/books/1', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            [
                                'id' => (string)$authors[2]->id,
                                'type' => 'authors',
                            ],
                        ]
                    ]
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
                    "type" => 'books',
                    "attributes" => [
                        'title' => 'Building an API with Laravel',
                        'description' => 'A book about API development',
                        'publication_year' => '2019',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ],
                    'relationships' => [
                        'authors' => [
                            'links' => [
                                'self' => route(
                                    'books.relationships.authors',
                                    ['id' => 1]
                                ),
                                'related' => route(
                                    'books.authors',
                                    ['id' => 1]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => $authors->get(2)->id,
                                    'type' => 'authors'
                                ]
                            ]
                        ]
                    ]

                ]
            ]);

        $this->assertDatabaseHas('books', [
            'id' => 1,
            'title' => 'Building an API with Laravel',
        ])->assertDatabaseHas('author_book', [
            'book_id' => 1,
            'author_id' => $authors[2]->id,
        ]);


    }

    /**
     * @test
     */
    public function it_validates_relationships_given_when_updating_a_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $book = factory(Book::class)->create();

        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));


        $this->patchJson('/api/v1/books/1', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            [
                                'id' => $authors[1]->id,
                                'type' => 'authors',
                            ],
                            [
                                'id' => (string)$authors[1]->id,
                                'type' => 'random',
                            ],
                        ]
                    ]
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.relationships.authors.data.0.id must be a string.',
                        'source' => [
                            'pointer' => '/data/relationships/authors/data/0/id',
                        ]
                    ],
                    [
                        'title' => 'Validation Error',
                        'details' => 'The selected data.relationships.authors.data.1.type is invalid.',
                        'source' => [
                            'pointer' => '/data/relationships/authors/data/1/type',
                        ]
                    ],


                ]
            ]);
    }

}
