<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapMarker extends Model
{
    protected $fillable = ['title', 'description', 'latitude', 'longitude', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_markers', 'map_marker_id', 'route_id')
            ->withPivot('order')
            ->orderBy('order');
    }
}
