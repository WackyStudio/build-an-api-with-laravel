<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends AbstractAPIModel
{

    protected $fillable = [
        'message',
    ];

    /**
     * @return string
     */
    public function type()
    {
        return 'comments';
    }
}
