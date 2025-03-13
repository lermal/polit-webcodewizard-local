<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()->hasRole($role) && !$request->user()->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
