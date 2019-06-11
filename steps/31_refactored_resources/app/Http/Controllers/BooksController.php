<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BooksCollection;
use App\Http\Resources\BooksResource;
use App\Http\Resources\JSONAPICollection;
use App\Http\Resources\JSONAPIResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = QueryBuilder::for(Book::class)->allowedSorts([
            'title',
            'publication_year',
            'created_at',
            'updated_at',
        ])->allowedIncludes('authors')->jsonPaginate();
        return new JSONAPICollection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBookRequest $request)
    {
        $book = Book::create([
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'publication_year' => $request->input('data.attributes.publication_year'),
        ]);
        return (new JSONAPIResource($book))
            ->response()
            ->header('Location', route('books.show', [
            'book' => $book,
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($book)
    {
        $query = QueryBuilder::for(Book::where('id', $book))
            ->allowedIncludes('authors')
            ->firstOrFail();
        return new JSONAPIResource($query);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->input('data.attributes'));
        return new JSONAPIResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response(null, 204);
    }
}
