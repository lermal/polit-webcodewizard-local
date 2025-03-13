<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapMarker;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        $usersCount = User::count();
        return view('admin.stats', compact('usersCount'));
    }

    public function users()
    {
        return view('admin.users');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function map()
    {
        $markers = MapMarker::all();
        return view('admin.map', compact('markers'));
    }
}
