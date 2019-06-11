<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class BooksCommentsRelationshipsController extends Controller
{
    /**
     * @var JSONAPIService
     */
    private $service;

    public function __construct(JSONAPIService $service)
    {

        $this->service = $service;
    }

    public function index(Book $book)
    {
        return $this->service->fetchRelationship($book, 'comments');
    }

    public function update(JSONAPIRelationshipRequest $request, Book $book)
    {
        return $this->service->updateToManyRelationships($book, 'comments', $request->input('data.*.id'));
    }

}
