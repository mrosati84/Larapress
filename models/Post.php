<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent {
    protected $primaryKey = 'ID';
    public $timestamps = false;

    public function postmeta()
    {
        return $this->hasMany('Postmeta');
    }
}
