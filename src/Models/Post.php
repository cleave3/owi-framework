<?php

namespace App\Models;

class Post extends Model
{
    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
