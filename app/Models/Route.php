<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'voting_enabled'
    ];

    public function markers()
    {
        return $this->belongsToMany(MapMarker::class, 'route_markers')
            ->withPivot('order')
            ->orderBy('route_markers.order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(RouteVote::class);
    }
}
