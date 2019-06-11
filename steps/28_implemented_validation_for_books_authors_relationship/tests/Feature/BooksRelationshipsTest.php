<?php


namespace Tests\Feature;


use App\Author;
use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksRelationshipsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_a_relationship_to_authors_adhering_to_json_api_spec()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);


        $this->getJson('/api/v1/books/1', [
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
                    'details' => 'The selected data.0.type is invalid.',
                    'source' => [
                        'pointer' => '/data/0/type',
                    ]
                ]
            ]
        ]);
    }

}
