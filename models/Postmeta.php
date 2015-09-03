<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Postmeta extends Eloquent {
    protected $primaryKey = 'meta_id';
    protected $table = 'postmeta';
}
