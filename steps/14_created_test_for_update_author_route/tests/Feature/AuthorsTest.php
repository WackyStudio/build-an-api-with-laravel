<?php

namespace Tests\Feature;

use App\Author;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthorsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_returns_an_author_as_a_resource_object()
    {
        $author = factory(Author::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/authors/1')
             ->assertStatus(200)
             ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => $author->name,
                        'created_at' => $author->created_at->toJSON(),
                        'updated_at' => $author->updated_at->toJSON(),
                    ]
                ]
        ]);
    }

    /**
     * @test
     */
    public function it_returns_all_authors_as_a_collection_of_resource_objects()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $authors = factory(Author::class, 3)->create();

        $this->get('/api/v1/authors')->assertStatus(200)->assertJson([
            "data" => [
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
     */
    public function it_can_create_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => 'John Doe',
                ]
            ]
        ])
             ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "authors",
                    "attributes" => [
                        'name' => 'John Doe',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ])->assertHeader('Location', url('/api/v1/authors/1'));

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }

    /**
     * @test
     */
    public function it_can_update_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();

        $this->patchJson('/api/v1/authors/1', [
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'name' => 'Jane Doe',
                ]
            ]
        ])->assertStatus(200)->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'authors',
                'attributes' => [
                    'name' => 'Jane Doe',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ],
            ]
        ]);

        $this->assertDatabaseHas('authors', [
            'id' => 1,
            'name' => 'Jane Doe',
        ]);
    }


}