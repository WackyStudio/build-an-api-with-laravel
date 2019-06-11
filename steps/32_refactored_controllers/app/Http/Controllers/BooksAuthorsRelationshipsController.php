<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\UpdateBooksAuthorsRelationshipsRequest;
use App\Http\Resources\AuthorsIdentifierResource;
use App\Http\Resources\JSONAPIIdentifierResource;
use App\Services\JSONAPIService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BooksAuthorsRelationshipsController extends Controller
{

    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    public function index(Book $book)
    {
        return $this->service->fetchRelationship($book, 'authors');
    }

    public function update(UpdateBooksAuthorsRelationshipsRequest $request, Book $book)
    {
        return $this->service->updateManyToManyRelationships($book, 'authors', $request->input('data.*.id'));
    }
}
