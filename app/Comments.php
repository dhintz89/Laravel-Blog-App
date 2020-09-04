<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

// Comment class instance will refer to comments table in database
class Comments extends Model {
    // restricts database columns from being modified
    protected $guarded = [];

    // user who wrote comment
    public function author() {
        return $this->belongsTo('App\User', 'from_user');
    }

    // returns post of any comment
    public function post() {
        return $this->belongsTo('App\Posts', 'on_post');
    }
}