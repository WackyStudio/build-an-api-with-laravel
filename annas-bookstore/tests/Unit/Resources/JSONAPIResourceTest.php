<?php

namespace Tests\Unit\Resources;

use App\Author;
use App\Book;
use App\Http\Resources\AuthorsIdentifierResource;
use App\Http\Resources\JSONAPIResource;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JSONAPIResourceTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * @return void
     */
    public function it_builds_relationship_attributes_from_config()
    {
        Config::set('jsonapi.resources.books.relationships', [
            [
                'type' => 'authors',
                'method' => 'authors',
            ]
        ]);

        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 3)->create();
        $book->authors()->sync($authors->pluck('id'));
        $book->load('authors');

        $resource = new JSONAPIResource($book);
        $request = new Request();
        $response = $resource->toResponse($request);
        TestResponse::fromBaseResponse($response)->assertJson([
           'data' => [
               'relationships' => [
                   'authors' => [
                       'links' => [
                           'self'    => route(
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
                           ],
                           [
                               'id' => $authors->get(2)->id,
                               'type' => 'authors'
                           ]
                       ]
                   ],
               ],
           ]
        ]);
    }
}