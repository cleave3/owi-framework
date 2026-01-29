<?php

namespace App\Models;

class Category extends Model
{
    protected $table = 'categories';

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
