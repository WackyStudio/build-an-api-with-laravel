<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\AuthorsCollection;
use Illuminate\Http\Request;

class BooksAuthorsRelatedController extends Controller
{

    public function index(Book $book)
    {
        return new AuthorsCollection($book->authors);
    }
}
