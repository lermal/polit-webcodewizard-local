<?php

namespace App\Http\Controllers;

use App\Models\MapMarker;
use App\Models\Route;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $markers = MapMarker::all();
        $routes = Route::all();
        return view('map.index', compact('markers', 'routes'));
    }

    public function storeMarker(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $marker = MapMarker::create([
            ...$validated,
            'created_by' => auth()->id()
        ]);

        return response()->json($marker);
    }

    public function updateMarker(Request $request, $id)
    {
        $marker = MapMarker::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $marker->update($validated);

        return response()->json($marker);
    }

    public function deleteMarker(MapMarker $marker)
    {
        $marker->delete();
        return response()->json(['success' => true]);
    }

    public function getMarkers()
    {
        try {
            $markers = MapMarker::select('id', 'title', 'description', 'latitude', 'longitude')->get();
            return response()->json([
                'markers' => $markers
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function getRoute($id)
    {
        $route = Route::with(['markers'])->findOrFail($id);
        return response()->json($route);
    }

    public function updateRoute(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'markers' => 'required|array',
            'markers.*' => 'exists:map_markers,id'
        ]);

        $route->update([
            'name' => $validated['name'],
            'description' => $validated['description']
        ]);

        // Обновляем маркеры
        $route->markers()->detach(); // Удаляем старые связи
        foreach ($validated['markers'] as $index => $markerId) {
            $route->markers()->attach($markerId, ['order' => $index]);
        }

        return response()->json($route->load('markers'));
    }
}
