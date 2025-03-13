<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('teacher')->get();
        return response()->json(['groups' => $groups]);
    }

    public function getTeacherGroups(User $teacher)
    {
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'Пользователь не является учителем'], 403);
        }

        $groups = $teacher->teacherGroups()->get();
        return response()->json(['groups' => $groups]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $teacher = User::findOrFail($request->teacher_id);
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'Пользователь не является учителем'], 403);
        }

        $group = Group::create($request->all());
        return response()->json(['message' => 'Группа создана', 'group' => $group]);
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json(['message' => 'Группа удалена']);
    }
}
