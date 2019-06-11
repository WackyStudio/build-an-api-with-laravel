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
        $this->withoutExceptionHandling();
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

}
