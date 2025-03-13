<?php

namespace App\Http\Controllers;

use App\Models\MapMarker;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function map()
    {
        $markers = MapMarker::all();
        return view('admin.map', compact('markers'));
    }
}
