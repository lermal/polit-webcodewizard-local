<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $user = User::find(auth()->id());

        $validator = Validator::make($request->all(), [
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'admin_password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user->name = $request->admin_name;
        $user->email = $request->admin_email;

        if ($request->filled('admin_password')) {
            $user->password = Hash::make($request->admin_password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Настройки успешно обновлены'
        ]);
    }
}
