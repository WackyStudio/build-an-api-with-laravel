<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
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
}
