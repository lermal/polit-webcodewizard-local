<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;

class TeacherController extends Controller
{
    public function map()
    {
        $routes = Route::with(['markers', 'creator'])->get();
        return view('user.teacher.map', compact('routes'));
    }
}
