<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users');
    }

    public function getUsers()
    {
        $users = User::with(['group'])->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'group' => $user->group ? $user->group->name : '-',
                'created_at' => $user->created_at->format('d.m.Y')
            ];
        });

        return response()->json(['data' => $users]);
    }

    public function show(User $user)
    {
        $user->load('group');
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'teacher', 'admin'])],
            'group_id' => 'nullable|exists:groups,id'
        ]);

        $user->update($request->only(['name', 'email', 'role', 'group_id']));

        return response()->json(['message' => 'Пользователь обновлен']);
    }
}
