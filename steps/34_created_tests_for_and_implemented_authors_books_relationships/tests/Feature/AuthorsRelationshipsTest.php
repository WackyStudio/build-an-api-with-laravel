<?php

namespace Tests\Feature;

use App\Author;
use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthorsRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_a_relationship_to_books_adhering_to_json_api_spec()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 3)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);


        $this->getJson('/api/v1/authors/1?include=books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => '1',
                     'type' => 'authors',
                     'relationships' => [
                         'books' => [
                             'links' => [
                                 'self' => route(
                                     'authors.relationships.books',
                                     ['id' => $author->id]
                                 ),
                                 'related' => route(
                                     'authors.books',
                                     ['id' => $author->id]
                                 ),
                             ],
                             'data' => [
                                 [
                                     'id' => $books->get(0)->id,
                                     'type' => 'books'
                                 ],
                                 [
                                     'id' => $books->get(1)->id,
                                     'type' => 'books'
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
    public function a_relationship_link_to_books_returns_all_related_books_as_resource_id_objects()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 3)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1/relationships/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     [
                         'id' => '1',
                         'type' => 'books',
                     ],
                     [
                         'id' => '2',
                         'type' => 'books',
                     ],
                     [
                         'id' => '3',
                         'type' => 'books',
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
        $authors = factory(Author::class)->create();
        $books = factory(Book::class, 10)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'books',
                ],
                [
                    'id' => '6',
                    'type' => 'books',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 5,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 6,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_modify_relationships_to_books_and_remove_relationships()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => [
                [
                    'id' => '1',
                    'type' => 'books',
                ],
                [
                    'id' => '2',
                    'type' => 'books',
                ],
                [
                    'id' => '5',
                    'type' => 'books',
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
            'author_id' => 1,
            'book_id' => 2,
        ])->assertDatabaseHas('author_book', [
            'author_id' => 1,
            'book_id' => 5,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 3,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 4,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_remove_all_relationships_to_books_with_an_empty_collection()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => []
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 1,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 2,
        ])->assertDatabaseMissing('author_book', [
            'author_id' => 1,
            'book_id' => 3,
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_a_404_not_found_when_trying_to_add_relationship_to_a_non_existing_book()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => [
                [
                    'id' => '5',
                    'type' => 'books',
                ],
                [
                    'id' => '6',
                    'type' => 'books',
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
        $authors = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => [
                [
                    'type' => 'books',
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
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
            'data' => [
                [
                    'id' => 5,
                    'type' => 'books',
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
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
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
    public function it_validates_that_the_type_member_has_a_value_of_books_when_updating_a_relationship()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 5)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->patchJson('/api/v1/authors/1/relationships/books',[
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
    public function it_can_get_all_related_books_as_resource_objects_from_related_link()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 3)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1/books',[
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     [
                         "id" => '1',
                         "type" => "books",
                         "attributes" => [
                             'title' => $books[0]->title,
                             'description' => $books[0]->description,
                             'publication_year' => $books[0]->publication_year,
                             'created_at' => $books[0]->created_at->toJSON(),
                             'updated_at' => $books[0]->updated_at->toJSON(),
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
                         ]
                     ],
                 ]
             ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_includes_related_resource_objects_when_an_include_query_param_is_given()
    {
        $author = factory(Author::class)->create();
        $books = factory(Book::class, 3)->create();
        $author->books()->sync($books->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1?include=books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
             ->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'id' => '1',
                     'type' => 'authors',
                     'relationships' => [
                         'books' => [
                             'links' => [
                                 'self' => route(
                                     'authors.relationships.books',
                                     ['id' => $author->id]
                                 ),
                                 'related' => route(
                                     'authors.books',
                                     ['id' => $author->id]
                                 ),
                             ],
                             'data' => [
                                 [
                                     'id' => (string)$books->get(0)->id,
                                     'type' => 'books'
                                 ],
                                 [
                                     'id' => (string)$books->get(1)->id,
                                     'type' => 'books'
                                 ],
                                 [
                                     'id' => (string)$books->get(2)->id,
                                     'type' => 'books'
                                 ]

                             ]
                         ]
                     ]
                 ],
                 'included' => [
                     [
                         "id" => '1',
                         "type" => "books",
                         "attributes" => [
                             'title' => $books[0]->title,
                             'description' => $books[0]->description,
                             'publication_year' => $books[0]->publication_year,
                             'created_at' => $books[0]->created_at->toJSON(),
                             'updated_at' => $books[0]->updated_at->toJSON(),
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
        $author = factory(Author::class)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1', [
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
        $this->withoutExceptionHandling();
        $authors = factory(Author::class, 3)->create();
        $books = factory(Book::class, 3)->create();

        $authors->each(function($author, $key) use($books){
            if($key === 0){
                $author->books()->sync($books->pluck('id'));
            }
        });

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/authors?include=books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[0]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[0]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => (string)$books->get(0)->id,
                                    'type' => 'books'
                                ],
                                [
                                    'id' => (string)$books->get(1)->id,
                                    'type' => 'books'
                                ],
                                [
                                    'id' => (string)$books->get(2)->id,
                                    'type' => 'books'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[1]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[1]->id]
                                ),
                            ],
                        ]
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[2]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[2]->id]
                                ),
                            ],
                        ]
                    ]
                ],
            ],
            'included' => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
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
        $authors = factory(Author::class, 3)->create();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/authors', [
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
        $this->withoutExceptionHandling();
        $authors = factory(Author::class, 3)->create();
        $books = factory(Book::class, 3)->create();

        $authors->each(function($author, $key) use($books){
            if($key === 0){
                $author->books()->sync($books->pluck('id'));
            }
        });

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->get('/api/v1/authors?include=books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[0]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[0]->id]
                                ),
                            ],
                            'data' => [
                                [
                                    'id' => (string)$books->get(0)->id,
                                    'type' => 'books'
                                ],
                                [
                                    'id' => (string)$books->get(1)->id,
                                    'type' => 'books'
                                ],
                                [
                                    'id' => (string)$books->get(2)->id,
                                    'type' => 'books'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[1]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[1]->id]
                                ),
                            ],
                        ]
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "authors",
                    'relationships' => [
                        'books' => [
                            'links' => [
                                'self' => route(
                                    'authors.relationships.books',
                                    ['id' => $authors[2]->id]
                                ),
                                'related' => route(
                                    'authors.books',
                                    ['id' => $authors[2]->id]
                                ),
                            ],
                        ]
                    ]
                ],
            ],
            'included' => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
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
                    ]
                ],
            ]
        ])->assertJsonMissing([
            'included' => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
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
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
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
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[0]->title,
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
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
                    ]
                ],
            ]
        ]);
    }
}