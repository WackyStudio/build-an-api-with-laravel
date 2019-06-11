<?php


namespace Tests\Feature;


use App\Author;
use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksAuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function a_user_cannot_create_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);

        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(403)->assertJson([
               'errors' => [
                  [
                      'title' => 'Access Denied Http Exception',
                      'details' => 'This action is unauthorized.',
                  ]
               ]
            ]);
    }

    /**
     * @test
     */
    public function an_admin_can_create_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);
        Passport::actingAs($user);

        $this->postJson('/api/v1/books', [
            'data' => [
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(201);

    }

    /** @test */
    public function a_user_cannot_update_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);
        $book = factory(Book::class)->create();

        $this->patchJson('/api/v1/books/1', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_update_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);
        Passport::actingAs($user);
        $book = factory(Book::class)->create();

        $this->patchJson('/api/v1/books/1', [
            'data' => [
                'id' => '1',
                'type' => 'books',
                'attributes' => [
                    'title' => 'Building an API with Laravel',
                    'description' => 'A book about API development',
                    'publication_year' => '2019',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_delete_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);
        $book = factory(Book::class)->create();

        $this->delete('/api/v1/books/1',[], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_delete_a_book()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);
        Passport::actingAs($user);
        $book = factory(Book::class)->create();

        $this->delete('/api/v1/books/1',[], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_can_fetch_a_list_of_books()
    {
        $user = factory(User::class)->create([
            'role' => 'user',
        ]);
        Passport::actingAs($user);
        $books = factory(Book::class, 3)->create();

        $this->get('/api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_fetch_a_list_of_books()
    {
        $user = factory(User::class)->create([
            'role' => 'admin',
        ]);
        Passport::actingAs($user);
        $books = factory(Book::class, 3)->create();

        $this->get('/api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function a_user_can_fetch_a_single_book()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'role' => 'user'
        ]);
        Passport::actingAs($user);

        $book = factory(Book::class)->create();

        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200);
    }

    /**
     * @test
     * @watch
     */
    public function an_admin_can_fetch_a_single_book()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'role' => 'admin'
        ]);
        Passport::actingAs($user);

        $book = factory(Book::class)->create();

        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200);
    }


    /** @test */
    public function a_user_cannot_modify_relationship_links_for_authors()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 10)->create();

        $user = factory(User::class)->create([
            'role' => 'user'
        ]);
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
        ])->assertStatus(403)->assertJson([
            'errors' => [
                [
                    'title' => 'Access Denied Http Exception',
                    'details' => 'This action is unauthorized.',
                ]
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_modify_relationship_links_for_authors()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 10)->create();

        $user = factory(User::class)->create([
            'role' => 'admin'
        ]);
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

    }



}
