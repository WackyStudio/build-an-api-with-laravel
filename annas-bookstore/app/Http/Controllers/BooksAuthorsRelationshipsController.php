<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\JSONAPIRelationshipRequest;
use App\Http\Requests\UpdateBooksAuthorsRelationshipsRequest;
use App\Http\Resources\AuthorsIdentifierResource;
use App\Services\JSONAPIService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BooksAuthorsRelationshipsController extends Controller
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
        return $this->service->fetchRelationship($book, 'authors');
    }

    public function update(JSONAPIRelationshipRequest $request, Book $book)
    {

        if(Gate::denies('admin-only')){
            throw new AuthorizationException('This action is unauthorized.');
        }

        return $this->service->updateManyToManyRelationships($book, 'authors', $request->input('data.*.id'));
    }
}
