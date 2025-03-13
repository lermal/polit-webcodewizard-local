<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'markers' => 'required|array',
            'markers.*' => 'exists:map_markers,id'
        ]);

        $route = Route::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_by' => auth()->id()
        ]);

        foreach ($validated['markers'] as $index => $markerId) {
            $route->markers()->attach($markerId, ['order' => $index]);
        }

        return response()->json($route->load('markers'));
    }

    public function show(Route $route)
    {
        return view('map.route', compact('route'));
    }
}
