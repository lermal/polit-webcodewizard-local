<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestRating extends Model
{
    protected $fillable = [
        'user_id',
        'test_id',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
