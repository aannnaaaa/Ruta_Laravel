<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'routes';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover_image',
        'is_public',
        'is_completed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function points()
    {
        return $this->hasMany(RoutePoint::class, 'route_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'route_tag', 'route_id', 'tag_id');
    }
}
