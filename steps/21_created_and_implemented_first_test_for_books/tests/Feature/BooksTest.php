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
     */
    public function it_returns_all_books_as_a_collection_of_resource_objects()
    {
        
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
     */
    public function it_can_sort_books_by_name_through_a_sort_query_parameter()
    {
        
    }

    /**
     * @test
     */
    public function it_can_sort_books_by_name_in_descending_order_through_a_sort_query_parameter()
    {
        
    }

    /**
     * @test
     */
    public function it_can_sort_books_by_multiple_attributes_through_a_sort_query_parameter()
    {
        
    }

    /**
     * @test
     */
    public function it_can_sort_books_by_multiple_attributes_in_descending_order_through_a_sort_query_parameter()
    {
        
    }

    /**
     * @test
     */
    public function it_can_paginate_books_through_a_page_query_parameter()
    {
        
    }

    /**
     * @test
     */
    public function it_can_paginate_books_through_a_page_query_parameter_and_show_different_pages()
    {
        
    }

}
