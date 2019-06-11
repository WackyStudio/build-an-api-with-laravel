<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\AuthorsCollection;
use App\Services\JSONAPIService;
use Illuminate\Http\Request;

class BooksAuthorsRelatedController extends Controller
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
        return $this->service->fetchRelated($book, 'authors');
    }
}
