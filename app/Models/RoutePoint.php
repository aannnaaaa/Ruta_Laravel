<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePoint extends Model
{
    protected $fillable = [
        'route_id',
        'name',
        'type',
        'address',
        'lat',
        'lng',
        'description',
        'image',
        'order_index'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'route_id');
    }
}
