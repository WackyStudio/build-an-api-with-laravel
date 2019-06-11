<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\UpdateBooksAuthorsRelationshipsRequest;
use App\Http\Resources\AuthorsIdentifierResource;
use App\Http\Resources\JSONAPIIdentifierResource;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BooksAuthorsRelationshipsController extends Controller
{

    public function index(Book $book)
    {
        return JSONAPIIdentifierResource::collection($book->authors);
    }

    public function update(UpdateBooksAuthorsRelationshipsRequest $request, Book $book)
    {
        $ids = $request->input('data.*.id');
        $book->authors()->sync($ids);
        return response(null, 204);
    }
}
