<?php


namespace Tests\Feature;


use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function it_returns_an_book_as_a_resource_object()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => $book->title,
                        'description' => $book->description,
                        'publication_year' => $book->publication_year,
                        'created_at' => $book->created_at->toJSON(),
                        'updated_at' => $book->updated_at->toJSON(),
                    ]
                ]
            ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_returns_all_books_as_a_collection_of_resource_objects()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $books = factory(Book::class, 3)->create();

        $this->get('/api/v1/books', [
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
     */
    public function it_can_create_an_book_from_a_resource_object()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_is_given_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_has_the_value_of_books_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_a_title_attribute_is_given_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_a_title_attribute_is_a_string_when_creating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_a_description_attribute_is_given_when_creating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_validates_that_a_description_attribute_is_a_string_when_creating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_validates_that_a_publication_year_attribute_is_given_when_creating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_validates_that_a_publication_year_attribute_is_a_string_when_creating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_can_update_an_book_from_a_resource_object()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_an_id_member_is_given_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_an_id_member_is_a_string_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_is_given_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_type_member_has_the_value_of_books_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_has_been_given_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_the_attributes_member_is_an_object_given_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_a_title_attribute_is_a_string_when_updating_an_book()
    {
        
    }

    /**
     * @test
     */
    public function it_validates_that_a_description_attribute_is_a_string_when_updating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_validates_that_a_publication_year_attribute_is_a_string_when_updating_an_book()
    {

    }

    /**
     * @test
     */
    public function it_can_delete_an_book_through_a_delete_request()
    {
        
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_books_by_title_through_a_sort_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $books = collect([
            'Building an API with Laravel',
            'Classes are our blueprints',
            'Adhering to the JSON:API Specification',
        ])->map(function($title){
            return factory(Book::class)->create([
                'title' => $title
            ]);
        });

        $this->get('/api/v1/books?sort=title', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' =>  'Adhering to the JSON:API Specification',
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
                        'title' => 'Building an API with Laravel',
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
                        'title' => 'Classes are our blueprints',
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
        
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_books_by_title_in_descending_order_through_a_sort_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $books = collect([
            'Building an API with Laravel',
            'Classes are our blueprints',
            'Adhering to the JSON:API Specification',
        ])->map(function($title){
            return factory(Book::class)->create([
                'title' => $title
            ]);
        });

        $this->get('/api/v1/books?sort=-title', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '2',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Classes are our blueprints',
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Building an API with Laravel',
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' =>  'Adhering to the JSON:API Specification',
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
    public function it_can_sort_books_by_multiple_attributes_through_a_sort_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $books = collect([
            'Building an API with Laravel',
            'Classes are our blueprints',
            'Adhering to the JSON:API Specification',
        ])->map(function($title){

            if($title === 'Building an API with Laravel'){
                return factory(Book::class)->create([
                    'title' => $title,
                    'publication_year' => '2019',
                ]);
            }

            return factory(Book::class)->create([
                'title' => $title,
                'publication_year' => '2018',
            ]);

        });

        $this->get('/api/v1/books?sort=publication_year,title', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' =>  'Adhering to the JSON:API Specification',
                        'description' => $books[2]->description,
                        'publication_year' => $books[2]->publication_year,
                        'created_at' => $books[2]->created_at->toJSON(),
                        'updated_at' => $books[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Classes are our blueprints',
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Building an API with Laravel',
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_sort_books_by_multiple_attributes_in_descending_order_through_a_sort_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $books = collect([
            'Building an API with Laravel',
            'Classes are our blueprints',
            'Adhering to the JSON:API Specification',
        ])->map(function($title){

            if($title === 'Building an API with Laravel'){
                return factory(Book::class)->create([
                    'title' => $title,
                    'publication_year' => '2019',
                ]);
            }

            return factory(Book::class)->create([
                'title' => $title,
                'publication_year' => '2018',
            ]);

        });

        $this->get('/api/v1/books?sort=-publication_year,title', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '1',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Building an API with Laravel',
                        'description' => $books[0]->description,
                        'publication_year' => $books[0]->publication_year,
                        'created_at' => $books[0]->created_at->toJSON(),
                        'updated_at' => $books[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "books",
                    "attributes" => [
                        'title' =>  'Adhering to the JSON:API Specification',
                        'description' => $books[2]->description,
                        'publication_year' => $books[2]->publication_year,
                        'created_at' => $books[2]->created_at->toJSON(),
                        'updated_at' => $books[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "books",
                    "attributes" => [
                        'title' => 'Classes are our blueprints',
                        'description' => $books[1]->description,
                        'publication_year' => $books[1]->publication_year,
                        'created_at' => $books[1]->created_at->toJSON(),
                        'updated_at' => $books[1]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_paginate_books_through_a_page_query_parameter()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $books = factory(Book::class, 10)->create();

        $this->get('/api/v1/books?page[size]=5&page[number]=1', [
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
                    "id" => '4',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[3]->title,
                        'description' => $books[3]->description,
                        'publication_year' => $books[3]->publication_year,
                        'created_at' => $books[3]->created_at->toJSON(),
                        'updated_at' => $books[3]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '5',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[4]->title,
                        'description' => $books[4]->description,
                        'publication_year' => $books[4]->publication_year,
                        'created_at' => $books[4]->created_at->toJSON(),
                        'updated_at' => $books[4]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('books.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('books.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('books.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
    }

    /**
     * @test
     * @watch
     */
    public function it_can_paginate_books_through_a_page_query_parameter_and_show_different_pages()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $books = factory(Book::class, 10)->create();

        $this->get('/api/v1/books?page[size]=5&page[number]=2', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "id" => '6',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[5]->title,
                        'description' => $books[5]->description,
                        'publication_year' => $books[5]->publication_year,
                        'created_at' => $books[5]->created_at->toJSON(),
                        'updated_at' => $books[5]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '7',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[6]->title,
                        'description' => $books[6]->description,
                        'publication_year' => $books[6]->publication_year,
                        'created_at' => $books[6]->created_at->toJSON(),
                        'updated_at' => $books[6]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '8',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[7]->title,
                        'description' => $books[7]->description,
                        'publication_year' => $books[7]->publication_year,
                        'created_at' => $books[7]->created_at->toJSON(),
                        'updated_at' => $books[7]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '9',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[8]->title,
                        'description' => $books[8]->description,
                        'publication_year' => $books[8]->publication_year,
                        'created_at' => $books[8]->created_at->toJSON(),
                        'updated_at' => $books[8]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '10',
                    "type" => "books",
                    "attributes" => [
                        'title' => $books[9]->title,
                        'description' => $books[9]->description,
                        'publication_year' => $books[9]->publication_year,
                        'created_at' => $books[9]->created_at->toJSON(),
                        'updated_at' => $books[9]->updated_at->toJSON(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('books.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('books.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => route('books.index', ['page[size]' => 5, 'page[number]' => 1]),
                'next' => null,
            ]
        ]);
    }

}
