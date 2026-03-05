<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'route_tag', 'tag_id', 'route_id');
    }
}
