<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends AbstractAPIModel
{

    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function type()
    {
        return 'authors';
    }

}
