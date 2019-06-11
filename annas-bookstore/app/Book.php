<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends AbstractAPIModel
{
    protected $fillable = [
        'title',
        'description',
        'publication_year',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return string
     */
    public function type()
    {
        return 'books';
    }

}
