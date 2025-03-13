<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteVote;
use Illuminate\Http\Request;

class PublicMapController extends Controller
{
    public function index()
    {
        $routes = Route::with(['markers', 'creator', 'votes'])
            ->withCount('votes')
            ->get();

        return view('map.public', compact('routes'));
    }

    public function startVoting(Route $route)
    {
        if (!auth()->user()->can('start_route_voting')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $route->update(['voting_enabled' => true]);
        return response()->json(['success' => true]);
    }

    public function vote(Route $route)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$route->voting_enabled) {
            return response()->json(['error' => 'Voting is not enabled for this route'], 403);
        }

        $vote = RouteVote::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'route_id' => $route->id
            ],
            ['vote' => request('vote')]
        );

        return response()->json([
            'success' => true,
            'votes_count' => $route->votes()->count()
        ]);
    }
}
