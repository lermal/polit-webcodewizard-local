<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteVote extends Model
{
    protected $fillable = ['user_id', 'route_id', 'vote'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
