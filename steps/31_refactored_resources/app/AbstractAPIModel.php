<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractAPIModel extends Model
{

    /**
     * @return string
     */
    abstract public function type();

    public function allowedAttributes(){
        return collect($this->attributes)->filter(function($item, $key){
            return !collect($this->hidden)->contains($key) && $key !== 'id';
        })->merge([
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
