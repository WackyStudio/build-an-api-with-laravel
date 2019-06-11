<?php

use Illuminate\Database\Seeder;
use \App\Author;
use \App\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Author::all()->each(function(Author $author){
           $books = factory(Book::class, 2)->create();
           $author->books()->sync($books->pluck('id'));
        });
    }
}
