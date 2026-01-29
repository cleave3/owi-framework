<?php

namespace App\Models;

class User extends Model
{
    protected $table = 'users';

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
